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
namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\User;
use wcf\data\user\UserList;
use wcf\system\WCF;

/**
 * Condition implementation for JCoins of a user.
 */
class UserJCoinsAmountCondition extends AbstractIntegerCondition implements IContentCondition, IUserCondition
{
    /**
     * @inheritdoc
     */
    protected $identifier = 'jCoinsAmount';

    /**
     * @inheritdoc
     */
    protected $label = 'wcf.user.condition.jcoins';

    /**
     * @inheritdoc
     */
    public function addUserCondition(Condition $condition, UserList $userList)
    {
        if ($condition->greaterThan !== null) {
            $userList->getConditionBuilder()->add('user_table.jCoinsAmount > ?', [$condition->greaterThan]);
        }
        if ($condition->lessThan !== null) {
            $userList->getConditionBuilder()->add('user_table.jCoinsAmount < ?', [$condition->lessThan]);
        }
    }

    /**
     * @inheritdoc
     */
    public function checkUser(Condition $condition, User $user)
    {
        if ($condition->greaterThan !== null && $user->jCoinsAmount <= $condition->greaterThan) {
            return false;
        }
        if ($condition->lessThan !== null && $user->jCoinsAmount >= $condition->lessThan) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function showContent(Condition $condition)
    {
        if (!WCF::getUser()->userID) {
            return false;
        }

        return $this->checkUser($condition, WCF::getUser());
    }
}
