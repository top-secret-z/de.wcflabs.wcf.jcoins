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
namespace wcf\system\jcoins\statement;

use wcf\data\DatabaseObject;
use wcf\data\jcoins\statement\JCoinsStatementAction;
use wcf\data\object\type\ObjectType;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\UserProfile;
use wcf\system\event\EventHandler;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;

/**
 * The default JCoins Statement.
 */
class DefaultJCoinsStatement
{
    /**
     * The parameters for the statement.
     */
    protected $parameters = [];

    /**
     * The object type for the statement.
     */
    protected $objectType;

    /**
     * The object class name.
     */
    protected $objectClassName;

    /**
     * The database object for the statement.
     */
    protected $object;

    /**
     * object id
     */
    protected $objectID;

    /**
     * Reverse mode flag
     */
    protected $reverseMode = false;

    /**
     * The return values.
     */
    protected $returnValuesLastObject;

    /**
     * DefaultJCoinsStatement constructor.
     */
    final public function __construct(ObjectType $objectType)
    {
        $this->objectType = $objectType;
    }

    /**
     * Calculate the amount for this object.
     */
    public function calculateAmount()
    {
        return $this->parameters['amount'] ?? (($this->objectType->amount !== null) ? $this->objectType->amount : (($this->objectType->defaultAmount !== null) ? $this->objectType->defaultAmount : 0));
    }

    /**
     * Calculate the retractable amount for this object.
     */
    public function calculateRetractableAmount()
    {
        return $this->parameters['amount'] ?? (($this->objectType->retractableAmount !== null) ? $this->objectType->retractableAmount : (($this->objectType->defaultAmount !== null) ? $this->objectType->defaultAmount * -1 : 0));
    }

    /**
     * Validates parameters.
     */
    public function validateParameters()
    {
        EventHandler::getInstance()->fireAction($this, 'validateParameters');

        if (!$this->getUserID()) {
            throw new UserInputException("userID");
        }
    }

    /**
     * Save the current statement in the Databse.
     */
    final public function save()
    {
        EventHandler::getInstance()->fireAction($this, 'save');

        $this->saveDatabase();

        EventHandler::getInstance()->fireAction($this, 'saved');

        return $this->returnValuesLastObject;
    }

    /**
     * Save the current statement in the Databse. Should not be called in a class.
     * Call save() instead.
     */
    protected function saveDatabase()
    {
        if (isset($this->parameters['time'])) {
            $time = $this->parameters['time'];
            unset($this->parameters['time']);
        } else {
            $time = false;
        }

        if ($this->reverseMode) {
            $amount = $this->calculateRetractableAmount();
            $this->setParameters(['reverseMode' => true]);
        } else {
            $amount = $this->calculateAmount();
        }

        // permission check for JCoins
        $hasPermission = false;
        if ($this->getUserID()) {
            $user = new UserProfile(new User($this->getUserID()));
            if ($user->getPermission('user.jcoins.canEarn')) {
                $hasPermission = true;
            }
        }

        if ($this->objectType->objectType == 'de.wcflabs.jcoins.statement.register') {
            $hasPermission = true;
        }

        if ($this->getUserID() && $amount && $hasPermission) {
            $data = [
                'objectTypeID' => $this->getObjectType()->getObjectID(),
                'objectID' => $this->objectID,
                'amount' => $amount,
                'additionalData' => \serialize($this->getParameters()),
                'userID' => $this->getUserID(),
                'time' => $time ?: TIME_NOW,
            ];

            $action = new JCoinsStatementAction([], 'create', [
                'data' => $data,
            ]);
            $returnValues = $action->executeAction();
            $this->returnValuesLastObject = $returnValues['returnValues'];

            // update coins for the user
            $userAction = new UserAction([$this->getUserID()], 'update', [
                'counters' => [
                    'jCoinsAmount' => $amount,
                ],
            ]);
            $userAction->executeAction();
        }
    }

    /**
     * Sets parameters and validate these if $validate is true.
     */
    public function setParameters(array $parameters, $validate = false)
    {
        EventHandler::getInstance()->fireAction($this, 'setParameters', $parameters);

        $this->parameters = \array_merge($this->parameters, $parameters);

        if ($validate) {
            $this->validateParameters();
        }
    }

    /**
     * Returns the parameters.
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the object type.
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Returns the object class name.
     */
    public function getObjectClassName()
    {
        return $this->getObjectType()->className;
    }

    /**
     * Sets and validate the object.
     */
    public function setObject(DatabaseObject $object)
    {
        $objectClass = $this->getObjectType()->objectClassName;

        if ($objectClass === null) {
            if ($object === null) {
                return;
            }

            throw new SystemException("objectType hasn't a object");
        }

        if ($objectClass != '*') {
            if (!$object instanceof $objectClass) {
                throw new SystemException("Object not matching class '" . $objectClass . "''");
            }
        }

        $this->object = $object;
        $this->objectID = $object->getObjectID();

        if ($objectClass == '*') {
            $this->setParameters([
                'objectClassName' => \get_class($object),
            ]);
        }
    }

    /**
     * Sets the objectID for this object.
     */
    public function setObjectID($objectID)
    {
        $this->objectID = $objectID;
    }

    /**
     * Returns the DatabaseObject.
     */
    public function getObject()
    {
        if ($this->getObjectType()->objectClassName === null) {
            return null;
        }

        if ($this->object === null && $this->objectID !== null) {
            $this->object = new $this->objectClassName($this->objectID);
        }

        return $this->object;
    }

    /**
     * Returns the user-id for the current statement.
     */
    public function getUserID()
    {
        if (isset($this->parameters['userID'])) {
            return $this->parameters['userID'];
        } elseif ($this->getObject()) {
            // we can not check, whether the interface IUserContent
            // is referenced, because it's not always referenced and
            // object decorators can have this method with the magic
            // method __call, so we must check this with is_callable
            if (\is_callable([$this->getObject(), 'getUserID'])) {
                return $this->getObject()->getUserID();
            }
        }

        return null;
    }

    /**
     * Returns the last statement.
     */
    public function getLastObject()
    {
        return $this->returnValuesLastObject;
    }

    /**
     * Enables the reverse mode
     */
    public function setReverse($bool = true)
    {
        $this->reverseMode = $bool;
    }
}
