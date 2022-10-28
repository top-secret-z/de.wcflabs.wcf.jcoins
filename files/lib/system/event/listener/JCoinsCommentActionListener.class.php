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

use wcf\system\exception\NamedUserException;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for new comments.
 */
class JCoinsCommentActionListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS) {
            return;
        }

        // check sufficient JCoins for comment and comment response creation
        if ($eventName == 'validateAction') {
            if (JCOINS_ALLOW_NEGATIVE) {
                return;
            }
            if (!WCF::getSession()->getPermission('user.jcoins.canEarn') || !WCF::getSession()->getPermission('user.jcoins.canUse')) {
                return;
            }

            switch ($eventObj->getActionName()) {
                case 'addComment':
                    $statement = UserJCoinsStatementHandler::getInstance()->getStatementProcessorInstance('de.wcflabs.jcoins.statement.comment.comment');
                    break;
                case 'addResponse':
                    $statement = UserJCoinsStatementHandler::getInstance()->getStatementProcessorInstance('de.wcflabs.jcoins.statement.comment.response');
                    break;
                default:
                    return true;
            }

            if ($statement->calculateAmount() < 0 && ($statement->calculateAmount() * -1) > WCF::getUser()->jCoinsAmount) {
                throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.jcoins.amount.tooLow'));
            }

            return true;
        }

        // assign JCoins
        switch ($eventObj->getActionName()) {
            case 'triggerPublication':
                foreach ($eventObj->getObjects() as $object) {
                    if ($object->userID) {
                        UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.comment.comment', $object->getDecoratedObject());
                    }
                }
                break;

                // 'enable' calls triggerPublication

            case 'triggerPublicationResponse':
                $params = $eventObj->getParameters();
                $response = $params['responses'][0];

                if ($response->userID) {
                    UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.comment.response', $response);
                }

                // no break
            case 'disable':
                foreach ($eventObj->getObjects() as $object) {
                    if ($object->userID) {
                        UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.comment.comment', $object->getDecoratedObject());
                    }
                }
                break;

            case 'delete':
                foreach ($eventObj->getObjects() as $object) {
                    if (!$object->isDisabled && $object->userID) {
                        UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.comment.comment', $object->getDecoratedObject());
                    }
                }
                break;
        }
    }
}
