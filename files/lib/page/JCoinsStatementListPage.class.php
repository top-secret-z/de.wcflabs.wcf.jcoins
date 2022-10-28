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
namespace wcf\page;

use wcf\data\jcoins\statement\JCoinsStatementList;
use wcf\system\WCF;

/**
 * Statement list for the user.
 */
class JCoinsStatementListPage extends SortablePage
{
    /**
     * @inheritdoc
     */
    public $enableTracking = true;

    /**
     * @inheritdoc
     */
    public $loginRequired = true;

    /**
     * @inheritdoc
     */
    public $neededModules = ['MODULE_JCOINS'];

    /**
     * @inheritdoc
     */
    public $itemsPerPage = JCOINS_STATEMENTS_PER_PAGE;

    /**
     * @inheritdoc
     */
    public $defaultSortField = 'statementID';

    /**
     * @inheritdoc
     */
    public $defaultSortOrder = 'DESC';

    /**
     * @inheritdoc
     */
    public $validSortFields = ['statementID', 'amount', 'time'];

    /**
     * @inheritdoc
     */
    public $objectListClassName = JCoinsStatementList::class;

    /**
     * @inheritdoc
     */
    public $neededPermissions = ['user.jcoins.canUse'];

    /**
     * Filter
     */
    public $objectType = 0;

    public $availableObjectTypes = [];

    /**
     * @inheritdoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (!empty($_REQUEST['objectType'])) {
            $this->objectType = \intval($_REQUEST['objectType']);
        }
    }

    /**
     * @inheritdoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        // object types and filter
        if (JCOINS_ALLOW_FILTER) {
            $this->availableObjectTypes = $this->objectList->getAvailableObjectTypes();

            if (!empty($this->objectType)) {
                $this->objectList->getConditionBuilder()->add('jcoins_statement.objectTypeID = ?', [$this->objectType]);
            }
        }

        $this->objectList->getConditionBuilder()->add("jcoins_statement.userID = ? AND jcoins_statement.isTrashed = 0", [WCF::getUser()->userID]);
    }

    /**
     * @inheritdoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        if (!JCOINS_ALLOW_FILTER) {
            $this->availableObjectTypes = [];
            $this->objectType = 0;
        }

        WCF::getTPL()->assign([
            'objectType' => $this->objectType,
            'availableObjectTypes' => $this->availableObjectTypes,
        ]);
    }
}
