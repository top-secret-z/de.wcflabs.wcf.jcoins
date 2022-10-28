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

use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\PurchasableJCoinsCacheBuilder;
use wcf\system\WCF;

/**
 * Object Editor for purchasable JCoins entries.
 */
class PurchasableJCoinsEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritdoc
     */
    protected static $baseClass = PurchasableJCoins::class;

    /**
     * Sets the show order of the offer.
     */
    public function setShowOrder($showOrder = 0)
    {
        $sql = "SELECT    MAX(showOrder)
            FROM    wcf" . WCF_N . "_purchasable_jcoins";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $maxShowOrder = $statement->fetchColumn();
        if (!$maxShowOrder) {
            $maxShowOrder = 0;
        }

        if (!$showOrder || $showOrder > $maxShowOrder) {
            $newShowOrder = $maxShowOrder + 1;
        } else {
            // shift other subscriptions
            $sql = "UPDATE    wcf" . WCF_N . "_purchasable_jcoins
                SET    showOrder = showOrder + 1
                WHERE    showOrder >= ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([
                $showOrder,
            ]);

            $newShowOrder = $showOrder;
        }

        $this->update([
            'showOrder' => $newShowOrder,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function resetCache()
    {
        PurchasableJCoinsCacheBuilder::getInstance()->reset();
    }
}
