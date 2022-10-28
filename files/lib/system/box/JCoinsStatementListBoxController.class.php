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
namespace wcf\system\box;

use wcf\data\jcoins\statement\JCoinsStatementList;
use wcf\system\WCF;

/**
 * Dynamic box controller implementation for a list of statements.
 */
class JCoinsStatementListBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    public $defaultLimit = 5;

    public $maximumLimit = 50;

    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'sidebarLeft',
        'sidebarRight',
    ];

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        return new JCoinsStatementList();
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        $templateName = 'boxJCoinsStatementList';

        return WCF::getTPL()->fetch($templateName, 'wcf', [
            'boxJCoinsList' => $this->objectList,
        ], true);
    }

    /**
     * @inheritDoc
     */
    public function hasContent()
    {
        if (!MODULE_JCOINS) {
            return false;
        }
        if (!WCF::getSession()->getPermission('user.jcoins.canSee')) {
            return false;
        }

        $this->sortField = 'time';
        $this->sortOrder = 'DESC';

        return parent::hasContent();
    }
}
