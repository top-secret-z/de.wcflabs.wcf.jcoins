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
namespace wcf\data\purchasable\jcoins;

use wcf\system\cache\builder\PurchasableJCoinsCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Represents a purchasable JCoins entry cache.
 */
class PurchasableJCoinsCache extends SingletonFactory
{
    /**
     * cached purchasable JCoins
     */
    protected $cachedPurchasableJCoins = [];

    /**
     * @see    \wcf\system\SingletonFactory::init()
     */
    protected function init()
    {
        $this->cachedPurchasableJCoins = PurchasableJCoinsCacheBuilder::getInstance()->getData();

        $orphanedPurchasableJCoins = [];
        foreach ($this->cachedPurchasableJCoins as $id => $purchasableJCoins) {
            if ($purchasableJCoins->availableUntil != 0 && $purchasableJCoins->availableUntil < TIME_NOW) {
                $orphanedPurchasableJCoins[] = $purchasableJCoins;

                unset($this->cachedPurchasableJCoins[$id]);
            }
        }

        if (!empty($orphanedPurchasableJCoins)) {
            $action = new PurchasableJCoinsAction($orphanedPurchasableJCoins, 'update', [
                'data' => [
                    'isDisabled' => 1,
                ],
            ]);
            $action->executeAction();
        }
    }

    /**
     * Returns all purchasable JCoins.
     */
    public function getPurchasableJCoins()
    {
        return $this->cachedPurchasableJCoins;
    }
}
