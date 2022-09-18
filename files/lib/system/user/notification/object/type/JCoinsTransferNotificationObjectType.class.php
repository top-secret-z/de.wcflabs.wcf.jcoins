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
namespace wcf\system\user\notification\object\type;

use wcf\data\jcoins\statement\JCoinsStatement;
use wcf\data\jcoins\statement\JCoinsStatementList;
use wcf\system\user\notification\object\JCoinsTransferNotificationObject;

/**
 * The user JCoins statement handler, which should be used to create statements.
 */
class JCoinsTransferNotificationObjectType extends AbstractUserNotificationObjectType
{
    /**
     * @inheritdoc
     */
    protected static $decoratorClassName = JCoinsTransferNotificationObject::class;

    /**
     * @inheritdoc
     */
    protected static $objectClassName = JCoinsStatement::class;

    /**
     * @inheritdoc
     */
    protected static $objectListClassName = JCoinsStatementList::class;
}
