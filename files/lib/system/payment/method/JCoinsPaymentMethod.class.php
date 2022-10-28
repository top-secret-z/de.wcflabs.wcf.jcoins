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
namespace wcf\system\payment\method;

use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\OptionUtil;
use wcf\util\StringUtil;

/**
 * The JCoins payment method.
 */
class JCoinsPaymentMethod extends AbstractPaymentMethod
{
    /**
     * @inheritdoc
     */
    public function getSupportedCurrencies()
    {
        $option = OptionUtil::parseSelectOptions(JCOINS_CURRENCY_TO_COINS);

        return \array_keys($option);
    }

    /**
     * @inheritdoc
     */
    public function getPurchaseButton($cost, $currency, $name, $token, $returnURL, $cancelReturnURL, $isRecurring = false, $subscriptionLength = 0, $subscriptionLengthUnit = '')
    {
        $jCoinsCurrency = OptionUtil::parseSelectOptions(JCOINS_CURRENCY_TO_COINS);

        if (!isset($jCoinsCurrency[$currency])) {
            throw new SystemException("Unknown currency for JCoins");
        }

        $conversion = \floatval($jCoinsCurrency[$currency]);
        $realCost = \round($conversion * $cost);
        $template = WCF::getTPL()->fetch('jcoinsPurchaseOverlay');
        $buttonUID = \substr(StringUtil::getRandomID(), 0, 5);
        $jCoinsAmount = WCF::getUser()->jCoinsAmount;

        WCF::getTPL()->assign([
            'cost' => $cost,
            'name' => $name,
            'token' => $token,
            'returnURL' => $returnURL,
            'currency' => $currency,
            'realCost' => $realCost,
            'template' => $template,
            'buttonUID' => $buttonUID,
            'jCoinsAmount' => $jCoinsAmount,
        ]);

        return WCF::getTPL()->fetch('jcoinsPurchaseButton');
    }
}
