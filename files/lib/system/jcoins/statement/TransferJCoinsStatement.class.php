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
namespace wcf\system\jcoins\statement;

use wcf\data\jcoins\statement\JCoinsStatementAction;
use wcf\data\user\UserAction;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\exception\UserInputException;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;

/**
 * The transfer JCoins statement.
 */
class TransferJCoinsStatement extends DefaultJCoinsStatement
{
    /**
     * Object type for transfers
     */
    const OBJECT_TYPE = 'de.wcflabs.jcoins.statement.transfer';

    /**
     * @inheritdoc
     */
    public function validateParameters()
    {
        parent::validateParameters();

        if (empty($this->parameters['amount'])) {
            throw new UserInputException('amount');
        }

        if (empty($this->parameters['author'])) {
            throw new UserInputException('author');
        }

        $userObject = UserProfileRuntimeCache::getInstance()->getObject($this->parameters['author']);

        if (!$userObject->getObjectID()) {
            throw new UserInputException('author');
        }

        $this->parameters['username'] = $userObject->getUsername();
    }

    /**
     * @inheritdoc
     */
    public function calculateAmount()
    {
        if (isset($this->parameters['amount'])) {
            return $this->parameters['amount'];
        }

        // no amount given
        return 0;
    }

    /**
     * @inheritdoc
     */
    protected function saveDatabase()
    {
        $parameters = $this->getParameters();

        if (isset($parameters['time'])) {
            $time = $parameters['time'];
            unset($parameters['time']);
        } else {
            $time = false;
        }

        unset($parameters['amount']);
        unset($parameters['userID']);

        $data = [
            'objectTypeID' => $this->getObjectType()->getObjectID(),
            'amount' => $this->calculateAmount(),
            'additionalData' => \serialize($parameters),
            'userID' => $this->parameters['userID'],
            'time' => $time ?: TIME_NOW,
        ];

        if ($this->parameters['userID'] && $this->calculateAmount()) {
            $action = new JCoinsStatementAction([], 'create', [
                'data' => $data,
            ]);
            $returnValues = $action->executeAction();
            $this->returnValuesLastObject = $returnValues['returnValues'];

            // update coins for the user
            $userAction = new UserAction([$this->parameters['userID']], 'update', [
                'counters' => [
                    'jCoinsAmount' => $this->calculateAmount(),
                ],
            ]);
            $userAction->executeAction();

            if (!(isset($parameters['moderative']) && $parameters['moderative']) && $this->calculateAmount() > 0) {
                $data = [
                    'amount' => $this->calculateAmount() * -1,
                    'userID' => $this->parameters['author'],
                    'author' => $this->parameters['userID'],
                ];

                if (isset($this->parameters['reason'])) {
                    $data['reason'] = $this->parameters['reason'];
                }

                UserJCoinsStatementHandler::getInstance()->create(self::OBJECT_TYPE, null, $data);
            }
        }
    }
}
