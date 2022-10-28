<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wcf\system\event\listener;

use Exception;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\User;
use wcf\system\reaction\ReactionHandler;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for reactions.
 */
class JCoinsReactionListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS || !MODULE_LIKE) {
            return;
        }

        $action = $eventObj->getActionName();
        if ($action != 'create' && $action != 'update' && $action != 'delete') {
            return;
        }

        // get data
        switch ($action) {
            case 'create':
                $returnValues = $eventObj->getReturnValues();
                $like = $returnValues['returnValues'];
                break;

            case 'update':
                $objects = $eventObj->getObjects();
                $like = $objects[0]->getDecoratedObject();
                break;

            case 'delete':
                $objects = $eventObj->getObjects();
                $like = $objects[0]->getDecoratedObject();
                break;
        }

        try {
            $objectType = ObjectTypeCache::getInstance()->getObjectType($like->objectTypeID);
            if (!$objectType) {
                return;
            }

            // must have object
            if ($action == 'delete') {
                $likeObject = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.like.likeableObject', $objectType->objectType)->getProcessor()->getObjectByID($like->objectID);
                $object = $likeObject->getDecoratedObject();
                if (!isset($object->userID) || $likeObject === null || !isset($likeObject->userID)) {
                    return;
                }
            }

            $likeObject = ReactionHandler::getInstance()->getLikeableObject($objectType->objectType, $like->objectID);
            $user = new User($likeObject->userID);
            if (!$user->userID) {
                return;
            }
        } catch (Exception $e) {
            return;
        }

        switch ($action) {
            case 'create':
                UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.reaction', $likeObject, ['usernameExecuter' => WCF::getUser()->username]);
                break;

            case 'update':
                UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.reaction', $likeObject, ['usernameExecuter' => WCF::getUser()->username]);

                UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.reaction', $likeObject, ['usernameExecuter' => WCF::getUser()->username]);
                break;

            case 'delete':
                UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.reaction', $likeObject, ['usernameExecuter' => WCF::getUser()->username]);
                break;
        }
    }
}
