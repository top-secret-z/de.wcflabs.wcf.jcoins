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
namespace wcf\acp\page;

use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Provides the purchasable jcoins transcation list.
 */
class PurchasableJCoinsTransactionLogListPage extends SortablePage
{
    /**
     * @inheritdoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.purchasableJCoins.transactionLog.list';

    /**
     * @inheritdoc
     */
    public $neededModules = ['MODULE_JCOINS', 'MODULE_PURCHASABLE_JCOINS'];

    /**
     * @inheritdoc
     */
    public $neededPermissions = ['admin.purchasableJCoins.canManage'];

    /**
     * @inheritdoc
     */
    public $defaultSortField = 'logTime';

    /**
     * @inheritdoc
     */
    public $defaultSortOrder = 'DESC';

    /**
     * @inheritdoc
     */
    public $objectListClassName = 'wcf\data\purchasable\jcoins\transaction\log\PurchasableJCoinsTransactionLogList';

    /**
     * @inheritdoc
     */
    public $validSortFields = ['logID', 'logMessage', 'purchasableJCoinsID', 'paymentMethodObjectTypeID', 'transactionID', 'logTime'];

    /**
     * transaction id
     */
    public $transactionID = '';

    /**
     * username
     */
    public $username = '';

    /**
     * @inheritdoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['transactionID'])) {
            $this->transactionID = StringUtil::trim($_REQUEST['transactionID']);
        }
        if (isset($_REQUEST['username'])) {
            $this->username = StringUtil::trim($_REQUEST['username']);
        }
    }

    /**
     * @inheritdoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        if ($this->transactionID) {
            $this->objectList->getConditionBuilder()->add('purchasable_jcoins_transaction_log.transactionID LIKE ?', ['%' . $this->transactionID . '%']);
        }

        if ($this->username) {
            $this->objectList->getConditionBuilder()->add('purchasable_jcoins_transaction_log.userID IN (SELECT userID FROM wcf' . WCF_N . '_user WHERE username LIKE ?)', ['%' . $this->username . '%']);
        }

        $this->objectList->sqlSelects = 'user_table.username, purchasable_jcoins.title';
        $this->objectList->sqlJoins = "LEFT JOIN wcf" . WCF_N . "_user user_table ON (user_table.userID = purchasable_jcoins_transaction_log.userID)";
        $this->objectList->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_purchasable_jcoins purchasable_jcoins ON (purchasable_jcoins.purchasableJCoinsID = purchasable_jcoins_transaction_log.purchasableJCoinsID)";
    }

    /**
     * @inheritdoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'transactionID' => $this->transactionID,
            'username' => $this->username,
        ]);
    }
}
