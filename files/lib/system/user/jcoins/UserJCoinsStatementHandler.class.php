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
namespace wcf\system\user\jcoins;

use wcf\data\DatabaseObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;

/**
 * The user JCoins statement handler, which should be used to create statements.
 */
class UserJCoinsStatementHandler extends SingletonFactory
{
    /**
     * All JCoins object types.
     */
    protected $objectTypes = [];

    /**
     * All JCoins statement object types sorted by ID.
     */
    protected $objectTypesByID = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('de.wcflabs.jcoins.statement.object');

        foreach ($this->objectTypes as $oT) {
            $this->objectTypesByID[$oT->getObjectID()] = $oT;
        }
    }

    /**
     * Creates a new statement with the given parameters. If you want to modify the author, use $parameter[author]
     * if you want to modify the time, use $parameter[time].
     */
    public function create($objectTypeName, ?DatabaseObject $object = null, array $parameters = [])
    {
        $newStatement = $this->getStatementProcessorInstance($objectTypeName);

        if ($object !== null) {
            $newStatement->setObject($object);
        }
        $newStatement->setParameters($parameters, true);

        // create the database object
        $newStatement->save();

        return $newStatement;
    }

    /**
     * Returns the object type by object-type-id.
     */
    public function getObjectTypeByID($id)
    {
        return $this->objectTypesByID[$id] ?? null;
    }

    /**
     * Revokes a statement with the given parameters.
     */
    public function revoke($objectTypeName, ?DatabaseObject $object = null, array $parameters = [])
    {
        $newStatement = $this->getStatementProcessorInstance($objectTypeName);
        $newStatement->setReverse();

        if ($object !== null) {
            $newStatement->setObject($object);
        }
        $newStatement->setParameters($parameters, true);

        // create the database object
        $newStatement->save();

        return $newStatement;
    }

    /**
     * returns all object types for JCoins statements.
     */
    public function getObjectTypes()
    {
        return $this->objectTypes;
    }

    /**
     * Returns the object type for a specific statement.
     */
    public function getObjectTypeByName($name)
    {
        if (!isset($this->objectTypes[$name])) {
            throw new SystemException("unknown statement object type name: " . $name);
        }

        return $this->objectTypes[$name];
    }

    /**
     * Returns a statement processor instance.
     */
    public function getStatementProcessorInstance($objectTypeName)
    {
        $objectType = $this->getObjectTypeByName($objectTypeName);

        // We clone the processor, because it is an new
        // instance of the object
        return clone $objectType->getProcessor();
    }
}
