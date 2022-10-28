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
namespace wcf\acp\page;

use wcf\page\SortablePage;

/**
 * Provides the purchasable jcoins list.
 */
class PurchasableJCoinsListPage extends SortablePage
{
    /**
     * @inheritdoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.purchasableJCoins.list';

    /**
     * @inheritdoc
     */
    public $neededModules = ['MODULE_JCOINS', 'MODULE_PURCHASABLE_JCOINS'];

    /**
     * @inheritdoc
     */
    public $neededPermissions = ['admin.purchasableJCoins.canManage'];

    /**
     * @inheritdoc
     */
    public $defaultSortField = 'purchasableJCoinsID';

    /**
     * @inheritdoc
     */
    public $objectListClassName = 'wcf\data\purchasable\jcoins\PurchasableJCoinsList';

    /**
     * @inheritdoc
     */
    public $validSortFields = ['purchasableJCoinsID', 'title', 'description', 'cost', 'jCoins', 'currency'];
}
