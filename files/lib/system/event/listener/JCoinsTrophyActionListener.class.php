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

use wcf\data\user\trophy\UserTrophyList;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for trophies when enabling / disabling trophies.
 */
class JCoinsTrophyActionListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS || !MODULE_TROPHY) {
            return;
        }

        // only toggle
        if ($eventObj->getActionName() != 'toggle') {
            return;
        }

        foreach ($eventObj->getObjects() as $object) {
            $trophy = $object->getDecoratedObject();

            // get user trophies
            $userTrophyList = new UserTrophyList();
            $userTrophyList->getConditionBuilder()->add('trophyID = ?', [$trophy->trophyID]);
            $userTrophyList->readObjects();
            $userTrophies = $userTrophyList->getObjects();
            if (empty($userTrophies)) {
                continue;
            }

            // step through user trophies and assign JCoins
            foreach ($userTrophies as $userTrophy) {
                if ($trophy->isDisabled) {
                    UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.trophy', $trophy, ['userID' => $userTrophy->userID]);
                }
                if (!$trophy->isDisabled) {
                    UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.trophy', $trophy, ['userID' => $userTrophy->userID]);
                }
            }
        }
    }
}
