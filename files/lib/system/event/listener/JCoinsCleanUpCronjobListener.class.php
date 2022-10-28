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
namespace wcf\system\event\listener;

use wcf\data\jcoins\statement\JCoinsStatementAction;
use wcf\data\jcoins\statement\JCoinsStatementList;

/**
 * Cleans up old jcoins statements.
 */
class JCoinsCleanUpCronjobListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS) {
            return;
        }

        if (!JCOINS_STATEMENTS_DELETEAFTER) {
            return;
        }

        $statementList = new JCoinsStatementList();
        $statementList->getConditionBuilder()->add("jcoins_statement.time < ?", [TIME_NOW - 86400 * JCOINS_STATEMENTS_DELETEAFTER]);
        if (JCOINS_STATEMENTS_DELETEONLYTRASHED) {
            $statementList->getConditionBuilder()->add('jcoins_statement.isTrashed = ?', [1]);
        }
        $statementList->readObjects();

        if (\count($statementList)) {
            $statementAction = new JCoinsStatementAction($statementList->getObjects(), 'delete');
            $statementAction->executeAction();
        }
    }
}
