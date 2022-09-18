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
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\system\worker\JCoinsTransferWorker;
use wcf\util\StringUtil;

/**
 * Transfer JCoins bulk action.
 */
class TransferJCoinsUserBulkProcessingAction extends AbstractUserBulkProcessingAction
{
    /**
     * amount
     */
    public $amount = 0;

    /**
     * transfer id
     */
    public $transferID = 0;

    /**
     * the reason for the transfer
     */
    public $reason = '';

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
            $data = WCF::getSession()->getVar(JCoinsTransferWorker::DATA_VARIABLE_NAME);
            if ($data === null) {
                $data = [];
            }
            $this->transferID = \count($data) + 1;
            $data[$this->transferID] = [
                'amount' => $this->amount,
                'reason' => $this->reason,
                'userIDs' => $objectList->getObjectIDs(),
            ];
            WCF::getSession()->register(JCoinsTransferWorker::DATA_VARIABLE_NAME, $data);
        }
    }

    /**
     * @inheritdoc
     */
    public function getHTML()
    {
        return WCF::getTPL()->fetch('transferJCoinsUserBulkProcessing', 'wcf', [
            'amount' => $this->amount,
            'reason' => $this->reason,
            'transferID' => $this->transferID,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function readFormParameters()
    {
        if (isset($_POST['amount'])) {
            $this->amount = \intval($_POST['amount']);
        }
        if (isset($_POST['reason'])) {
            $this->reason = StringUtil::trim($_POST['reason']);
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if (empty($this->reason)) {
            throw new UserInputException('reason');
        }

        if ($this->amount == 0) {
            throw new UserInputException('amount');
        }
    }
}
