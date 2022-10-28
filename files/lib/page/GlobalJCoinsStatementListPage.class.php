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

use wcf\data\jcoins\statement\GlobaleJCoinsStatementList;
use wcf\data\user\User;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Global statement list page.
 */
class GlobalJCoinsStatementListPage extends SortablePage
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
    public $validSortFields = ['statementID', 'amount', 'time', 'username'];

    /**
     * @inheritdoc
     */
    public $objectListClassName = GlobaleJCoinsStatementList::class;

    /**
     * @inheritdoc
     */
    public $neededPermissions = ['mod.jcoins.canSeeTransferList'];

    /**
     * User
     */
    public $user;

    /**
     * Filter
     */
    public $username = '';

    public $objectType = '';

    public $availableObjectTypes = [];

    /**
     * @inheritdoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (!empty($_REQUEST['username'])) {
            $this->username = StringUtil::trim($_REQUEST['username']);
        }
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

        // object types
        $this->availableObjectTypes = $this->objectList->getAvailableObjectTypes();

        // filter
        if (!empty($this->username)) {
            $user = User::getUserByUsername($this->username);
            if ($user->userID) {
                $this->objectList->getConditionBuilder()->add('jcoins_statement.userID = ?', [$user->userID]);
            } else {
                $this->objectList->getConditionBuilder()->add('jcoins_statement.userID = ?', [0]);
            }
        }

        if (!empty($this->objectType)) {
            $this->objectList->getConditionBuilder()->add('jcoins_statement.objectTypeID = ?', [$this->objectType]);
        }
    }

    /**
     * @inheritdoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'username' => $this->username,
            'objectType' => $this->objectType,
            'availableObjectTypes' => $this->availableObjectTypes,
        ]);
    }
}
