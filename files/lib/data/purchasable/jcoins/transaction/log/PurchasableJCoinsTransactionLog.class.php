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
namespace wcf\data\purchasable\jcoins\transaction\log;

use wcf\data\DatabaseObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\purchasable\jcoins\PurchasableJCoins;
use wcf\data\user\User;

/**
 * Represents a purchasable JCoins transaction log entry.
 */
class PurchasableJCoinsTransactionLog extends DatabaseObject
{
    /**
     * @inheritdoc
     */
    protected static $databaseTableName = 'purchasable_jcoins_transaction_log';

    /**
     * @inheritdoc
     */
    protected static $databaseTableIndexName = 'logID';

    /**
     * user object
     */
    protected $user;

    /**
     * paid subscription object
     */
    protected $purchasableJCoins;

    /**
     * Returns the payment method of this transaction.
     */
    public function getPaymentMethodName()
    {
        $objectType = ObjectTypeCache::getInstance()->getObjectType($this->paymentMethodObjectTypeID);

        return $objectType->objectType;
    }

    /**
     * Returns transaction details.
     */
    public function getTransactionDetails()
    {
        return \unserialize($this->transactionDetails);
    }

    /**
     * Returns the user of this transaction.
     */
    public function getUser()
    {
        if ($this->user === null) {
            $this->user = new User($this->userID);
        }

        return $this->user;
    }

    /**
     * Returns the purchasable JCoins of this transaction.
     */
    public function getSubscription()
    {
        if ($this->purchasableJCoins === null) {
            $this->purchasableJCoins = new PurchasableJCoins($this->purchasableJCoinsID);
        }

        return $this->purchasableJCoins;
    }
}
