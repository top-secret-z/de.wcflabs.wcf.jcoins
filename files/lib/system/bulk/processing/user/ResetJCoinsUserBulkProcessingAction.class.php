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
namespace wcf\system\bulk\processing\user;

use wcf\data\DatabaseObjectList;
use wcf\data\user\UserList;
use wcf\system\WCF;
use wcf\system\worker\JCoinsResetWorker;

/**
 * Reset JCoins bulk action.
 */
class ResetJCoinsUserBulkProcessingAction extends AbstractUserBulkProcessingAction
{
    /**
     * email text
     * @var    string
     */
    public $resetID = 0;

    /**
     * @inheritdoc
     */
    public function executeAction(DatabaseObjectList $objectList)
    {
        if (!($objectList instanceof UserList)) {
            return;
        }

        if (\count($objectList)) {
            // save config in session
            $data = WCF::getSession()->getVar(JCoinsResetWorker::DATA_VARIABLE_NAME);
            if ($data === null) {
                $data = [];
            }
            $this->resetID = \count($data) + 1;
            $data[$this->resetID] = [
                'userIDs' => $objectList->getObjectIDs(),
            ];
            WCF::getSession()->register(JCoinsResetWorker::DATA_VARIABLE_NAME, $data);
        }
    }

    /**
     * @inheritdoc
     */
    public function getHTML()
    {
        return WCF::getTPL()->fetch('resetJCoinsUserBulkProcessing', 'wcf', [
            'resetID' => $this->resetID,
        ]);
    }
}
