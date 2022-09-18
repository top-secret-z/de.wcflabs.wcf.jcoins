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
namespace wcf\data\jcoins\statement;

use wcf\data\DatabaseObjectList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\WCF;

/**
 * JCoins statement list.
 */
class JCoinsStatementList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = JCoinsStatement::class;

    /**
     * Returns a list of available object types.
     */
    public function getAvailableObjectTypes()
    {
        $types = [];
        $sql = "SELECT    DISTINCT objectTypeID
                FROM    wcf" . WCF_N . "_jcoins_statement
                WHERE    userID = ? AND isTrashed = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID, 0]);
        while ($row = $statement->fetchArray()) {
            if ($row['objectTypeID']) {
                $objectType = ObjectTypeCache::getInstance()->getObjectType($row['objectTypeID']);
                $types[$row['objectTypeID']] = WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.title.' . $objectType->objectType);
            }
        }
        \ksort($types);

        return $types;
    }
}
