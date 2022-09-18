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
namespace wcf\page;

use wcf\data\purchasable\jcoins\PurchasableJCoinsCache;
use wcf\system\WCF;

/**
 * Purchasable JCoins Page.
 */
class PurchasableJCoinsPage extends AbstractPage
{
    /**
     * @inheritdoc
     */
    public $loginRequired = true;

    /**
     * @inheritdoc
     */
    public $neededModules = ['MODULE_PURCHASABLE_JCOINS', 'MODULE_JCOINS'];

    /**
     * @inheritdoc
     */
    public $neededPermissions = ['user.jcoins.canUse'];

    /**
     * list of available purchasable JCoins
     */
    public $purchasableJCoins = [];

    /**
     * @inheritdoc
     */
    public function readData()
    {
        parent::readData();

        // get available subscriptions
        $this->purchasableJCoins = PurchasableJCoinsCache::getInstance()->getPurchasableJCoins();
    }

    /**
     * @inheritdoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'purchasableJCoins' => $this->purchasableJCoins,
        ]);
    }
}
