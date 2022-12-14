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
namespace wcf\system\payment\type;

use wcf\data\purchasable\jcoins\PurchasableJCoins;
use wcf\data\purchasable\jcoins\transaction\log\PurchasableJCoinsTransactionLogAction;
use wcf\data\user\User;
use wcf\system\exception\SystemException;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Purchasable JCoins payment type.
 */
class PurchasableJCoinsPaymentType extends AbstractPaymentType
{
    /**
     * @inheritdoc
     */
    public function processTransaction($paymentMethodObjectTypeID, $token, $amount, $currency, $transactionID, $status, $transactionDetails)
    {
        $user = $purchasableJCoins = null;
        try {
            $tokenParts = \explode(':', $token);
            if (\count($tokenParts) != 2) {
                throw new SystemException('invalid token');
            }
            [$userID, $purchasableJCoinsID] = $tokenParts;

            // get user object
            $user = new User(\intval($userID));
            if (!$user->userID) {
                throw new SystemException('invalid user');
            }

            // get subscription object
            $purchasableJCoins = new PurchasableJCoins(\intval($purchasableJCoinsID));
            if (!$purchasableJCoins->getObjectID()) {
                throw new SystemException('invalid purchasableJCoins' . \var_dump($token));
            }

            // check for 'duplicate' transactionID
            $sql = "SELECT    COUNT(*) AS count
                    FROM    wcf" . WCF_N . "_purchasable_jcoins_transaction_log
                    WHERE    paymentMethodObjectTypeID = ? AND transactionID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$paymentMethodObjectTypeID, $transactionID]);
            if ($statement->fetchColumn()) {
                throw new SystemException('transaction already processed');
            }

            $logMessage = '';
            if ($status == 'completed') {
                // validate payment amout
                if ($amount != $purchasableJCoins->cost || $currency != $purchasableJCoins->currency) {
                    throw new SystemException('invalid payment amount');
                }

                UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.purchasableJCoins', null, [
                    'amount' => $purchasableJCoins->jCoins,
                    'userID' => $user->userID,
                    'title' => WCF::getLanguage()->get($purchasableJCoins->title),
                ]);

                $logMessage = 'payment completed';
            }
            if ($status == 'reversed') {
                UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.purchasableJCoins', null, [
                    'amount' => $purchasableJCoins->jCoins * -1,
                    'userID' => $user->userID,
                    'title' => WCF::getLanguage()->get($purchasableJCoins->title),
                ]);

                $logMessage = 'payment reversed';
            }
            if ($status == 'canceled_reversal') {
                UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.purchasableJCoins', null, [
                    'amount' => $purchasableJCoins->jCoins,
                    'userID' => $user->userID,
                    'title' => WCF::getLanguage()->get($purchasableJCoins->title),
                ]);
                $logMessage = 'reversal canceled';
            }

            $action = new PurchasableJCoinsTransactionLogAction([], 'create', ['data' => [
                'userID' => $user->userID,
                'purchasableJCoinsID' => $purchasableJCoins->purchasableJCoinsID,
                'paymentMethodObjectTypeID' => $paymentMethodObjectTypeID,
                'logTime' => TIME_NOW,
                'transactionID' => $transactionID,
                'logMessage' => $logMessage,
                'transactionDetails' => \serialize($transactionDetails),
            ]]);
            $action->executeAction();
        } catch (SystemException $e) {
            $action = new PurchasableJCoinsTransactionLogAction([], 'create', ['data' => [
                'userID' => ($user !== null ? $user->userID : null),
                'purchasableJCoinsID' => ($purchasableJCoins !== null) ? $purchasableJCoins->purchasableJCoinsID : null,
                'paymentMethodObjectTypeID' => $paymentMethodObjectTypeID,
                'logTime' => TIME_NOW,
                'transactionID' => $transactionID,
                'logMessage' => $e->getMessage(),
                'transactionDetails' => \serialize($transactionDetails),
            ]]);
            $action->executeAction();

            throw $e;
        }
    }
}
