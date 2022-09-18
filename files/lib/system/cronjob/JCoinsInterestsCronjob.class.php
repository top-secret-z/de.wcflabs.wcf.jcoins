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
namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\user\UserEditor;
use wcf\data\user\UserProfileList;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Cronjob for JCoins Interests
 */
class JCoinsInterestsCronjob extends AbstractCronjob
{
    /**
     * @inheritdoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        // only if configured
        if (!MODULE_JCOINS || !JCOINS_INTERESTS_ENABLE || !JCOINS_INTERESTS_RATE) {
            return;
        }

        // set next execution
        $month = \date('n');
        $year = \date('Y');

        switch (JCOINS_INTERESTS_PERIOD) {
            case 'week':
                $exec = \strtotime('next monday midnight');
                break;
            case 'twoweek':
                $exec = \strtotime('next monday midnight');
                $exec += 7 * 86400;
                break;

            case 'month':
                $exec = \strtotime('first day of next month midnight');
                break;

            case 'quarter':
                if ($month < 4) {
                    $exec = \strtotime('1st April ' . (string)$year . ' midnight');
                } elseif ($month < 7) {
                    $exec = \strtotime('1st July ' . (string)$year . ' midnight');
                } elseif ($month < 10) {
                    $exec = \strtotime('1st October ' . (string)$year . ' midnight');
                } else {
                    $exec = \strtotime('1st January ' . (string)($year + 1) . ' midnight');
                }
                break;

            case 'halfyear':
                if ($month > 6) {
                    $exec = \strtotime('1st January ' . (string)($year + 1) . ' midnight');
                } else {
                    $exec = \strtotime('1st July ' . (string)$year . ' midnight');
                }
                break;

            case 'year':
                $exec = \strtotime('1st January ' . (string)($year + 1) . ' midnight');
                break;
        }

        // update user w/out time or if period was changed
        $sql = "UPDATE    wcf" . WCF_N . "_user
                SET        jCoinsInterests = ?, jCoinsInterestsPeriod = ?
                WHERE    jCoinsInterests = ? OR jCoinsInterestsPeriod <> ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$exec, JCOINS_INTERESTS_PERIOD, 0, JCOINS_INTERESTS_PERIOD]);

        // get interests / 250 at a time
        $userList = new UserProfileList();
        $userList->getConditionBuilder()->add('user_table.jCoinsInterests > ?', [0]);
        $userList->getConditionBuilder()->add('user_table.jCoinsInterests < ?', [TIME_NOW]);
        $userList->sqlLimit = 250;
        $userList->readObjects();
        $users = $userList->getObjects();

        if (!empty($users)) {
            foreach ($users as $user) {
                $editor = new UserEditor($user->getDecoratedObject());
                $editor->update([
                    'jCoinsInterests' => $exec,
                    'jCoinsInterestsPeriod' => JCOINS_INTERESTS_PERIOD,
                ]);

                // only if must pay and jcoins > 0
                if (!$user->getPermission('user.jcoins.mustPayInterest') || $user->jCoinsAmount < 1) {
                    continue;
                }
                $interests = \intval(\round($user->jCoinsAmount * JCOINS_INTERESTS_RATE / 100));
                if ($interests != 0) {
                    UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.interests', null, [
                        'amount' => $interests,
                        'userID' => $user->userID,
                        'moderative' => true,
                    ]);
                }
            }
        }
    }
}
