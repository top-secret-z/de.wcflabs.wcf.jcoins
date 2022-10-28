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
namespace wcf\system\worker;

use InvalidArgumentException;
use wcf\data\jcoins\statement\JCoinsStatementAction;
use wcf\data\jcoins\statement\JCoinsStatementList;
use wcf\data\user\UserAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * JCoins reset worker, which reset the user.
 */
class JCoinsResetWorker extends AbstractWorker
{
    /**
     * Transfer data session variable name.
     */
    const DATA_VARIABLE_NAME = 'JCOINS_RESET_WORKER_DATA';

    /**
     * condition builder object
     */
    protected $conditions;

    /**
     * @inheritdoc
     */
    protected $limit = 25;

    /**
     * transfer data
     */
    protected $resetData;

    /**
     * @inheritdoc
     */
    public function validate()
    {
        WCF::getSession()->checkPermissions(['admin.jcoins.canMassProcess']);

        if (!isset($this->parameters['resetID'])) {
            throw new InvalidArgumentException("resetID missing");
        }

        $userResetData = WCF::getSession()->getVar(self::DATA_VARIABLE_NAME);

        if (!isset($userResetData[$this->parameters['resetID']])) {
            throw new InvalidArgumentException("resetID '" . $this->parameters['resetID'] . "' is invalid");
        }

        $userResetData = $userResetData[$this->parameters['resetID']];

        if (isset($userResetData['conditions']) && $userResetData['conditions'] instanceof PreparedStatementConditionBuilder) {
            $this->conditions = $userResetData['conditions'];
        } elseif (isset($userResetData['userIDs']) && \is_array($userResetData['userIDs'])) {
            $this->conditions = new PreparedStatementConditionBuilder();
            $this->conditions->add("user_table.userID IN (?)", [$userResetData['userIDs']]);
        } else {
            // match all users
            $this->conditions = new PreparedStatementConditionBuilder();
        }

        $this->resetData = $userResetData;
    }

    /**
     * @inheritdoc
     */
    public function countObjects()
    {
        $sql = "SELECT    COUNT(*) AS count
                FROM    wcf" . WCF_N . "_user user_table
                " . $this->conditions;
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($this->conditions->getParameters());

        $this->count = $statement->fetchSingleColumn();
    }

    /**
     * @inheritdoc
     */
    public function getProgress()
    {
        $progress = parent::getProgress();

        if ($progress == 100) {
            // clear session
            $data = WCF::getSession()->getVar(self::DATA_VARIABLE_NAME);
            unset($data[$this->parameters['resetID']]);
            WCF::getSession()->register(self::DATA_VARIABLE_NAME, $data);
        }

        return $progress;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        // get users
        $sql = "SELECT        user_option.*, user_table.*
                FROM        wcf" . WCF_N . "_user user_table
                LEFT JOIN    wcf" . WCF_N . "_user_option_value user_option
                ON        (user_option.userID = user_table.userID)
                " . $this->conditions . "
                ORDER BY    user_table.userID";
        $statement = WCF::getDB()->prepareStatement($sql, $this->limit, ($this->limit * $this->loopCount));
        $statement->execute($this->conditions->getParameters());
        $users = [];
        $userIDs = [];
        while ($user = $statement->fetchObject('\wcf\data\user\User')) {
            $users[] = $user;
            $userIDs[] = $user->getObjectID();
        }

        $action = new UserAction($users, 'update', [
            'data' => [
                'jCoinsAmount' => 0,
            ],
        ]);
        $action->executeAction();

        $statementList = new JCoinsStatementList();
        $statementList->getConditionBuilder()->add("jcoins_statement.userID IN (?)", [$userIDs]);
        $statementList->readObjects();

        if (\count($statementList)) {
            $statementAction = new JCoinsStatementAction($statementList->getObjects(), 'delete');
            $statementAction->executeAction();
        }
    }

    /**
     * @inheritdoc
     */
    public function getProceedURL()
    {
        return LinkHandler::getInstance()->getLink('UserList');
    }
}
