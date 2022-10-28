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

use wcf\data\DatabaseObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\payment\method\JCoinsPaymentMethod;
use wcf\system\payment\method\PaymentMethodHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a purchasable JCoins entry.
 */
class PurchasableJCoins extends DatabaseObject
{
    /**
     * @inheritdoc
     */
    protected static $databaseTableName = 'purchasable_jcoins';

    /**
     * @inheritdoc
     */
    protected static $databaseTableIndexName = 'purchasableJCoinsID';

    /**
     * Returns the description for this offer.
     */
    public function getDescription($stripHTML = false)
    {
        if ($this->useHTML) {
            if ($stripHTML) {
                return StringUtil::stripHTML(WCF::getLanguage()->get($this->description));
            }

            return WCF::getLanguage()->get($this->description);
        }

        return \nl2br(StringUtil::encodeHTML(WCF::getLanguage()->get($this->description)));
    }

    /**
     * Returns list of purchase buttons.
     */
    public function getPurchaseButtons()
    {
        $objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.payment.type', 'de.wcflabs.jcoins.payment.type.purchasableJCoins');
        $buttons = [];
        foreach (PaymentMethodHandler::getInstance()->getPaymentMethods() as $paymentMethod) {
            // check supported currencies
            if (!\in_array($this->currency, $paymentMethod->getSupportedCurrencies())) {
                continue;
            }

            // jcoins can't buy with jcoins
            if ($paymentMethod instanceof JCoinsPaymentMethod) {
                continue;
            }

            $buttons[] = $paymentMethod->getPurchaseButton($this->cost, $this->currency, WCF::getLanguage()->get($this->title), $objectTypeID . ':' . WCF::getUser()->userID . ':' . $this->purchasableJCoinsID, LinkHandler::getInstance()->getLink('PurchasableJCoinsReturn'), LinkHandler::getInstance()->getLink());
        }

        return $buttons;
    }
}
