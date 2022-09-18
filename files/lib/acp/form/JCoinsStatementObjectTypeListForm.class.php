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
namespace wcf\acp\form;

use wcf\data\object\type\ObjectTypeAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Provides the jcoins statement object type option form.
 */
class JCoinsStatementObjectTypeListForm extends AbstractForm
{
    /**
     * The definition name for statement objects.
     */
    const DEFINITION_NAME = 'de.wcflabs.jcoins.statement.object';

    /**
     * All object types with the definition self::DEFINITION_NAME which are
     * editable.
     */
    public $objectTypes = [];

    /**
     * All object types categories.
     */
    public $objectTypesCategories = [];

    /**
     * All object types sorted by category with the definition self::DEFINITION_NAME
     */
    public $objectTypesByCategory = [];

    /**
     * All amount values for all object types.
     */
    public $amount = [];

    /**
     * All retractable amount values for all object types.
     */
    public $retractableAmount = [];

    /**
     * @inheritdoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.jcoinsStatementObjectType';

    /**
     * @inheritdoc
     */
    public function readData()
    {
        $this->objectTypes = ObjectTypeCache::getInstance()->getObjectTypes(self::DEFINITION_NAME);
        foreach ($this->objectTypes as $id => $objectType) {
            if ($objectType->editable === "0") {
                unset($this->objectTypes[$id]);
            }
        }

        foreach ($this->objectTypes as $objectType) {
            if (!\in_array($objectType->category, $this->objectTypesCategories)) {
                $this->objectTypesCategories[] = $objectType->category;
                $this->objectTypesByCategory[$objectType->category] = [];
            }

            $this->objectTypesByCategory[$objectType->category][] = $objectType;
        }

        if (empty($_POST)) {
            foreach ($this->objectTypes as $objectType) {
                $this->amount[$objectType->objectTypeID] = ($objectType->amount !== null) ? $objectType->amount : (($objectType->defaultAmount) ?: 0);

                if ($objectType->retractable === "1") {
                    $this->retractableAmount[$objectType->objectTypeID] = ($objectType->retractableAmount !== null) ? $objectType->retractableAmount : (($objectType->defaultAmount) ? ($objectType->defaultAmount * -1) : 0);
                }
            }
        }

        parent::readData();
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        if (isset($_POST['amount']) && \is_array($_POST['amount'])) {
            $this->amount = ArrayUtil::toIntegerArray($_POST['amount']);
        }
        if (isset($_POST['retractableAmount']) && \is_array($_POST['retractableAmount'])) {
            $this->retractableAmount = ArrayUtil::toIntegerArray($_POST['retractableAmount']);
        }
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        parent::save();

        foreach ($this->objectTypes as $objectType) {
            $data = [];
            if ($objectType->editable !== "0" && isset($this->amount[$objectType->objectTypeID]) && $objectType->amount !== $this->amount[$objectType->objectTypeID]) {
                $data['amount'] = $this->amount[$objectType->objectTypeID];
            }

            if ($objectType->editable !== "0" && $objectType->retractable !== "0" && isset($this->retractableAmount[$objectType->objectTypeID]) && $objectType->retractableAmount !== $this->retractableAmount[$objectType->objectTypeID]) {
                $data['retractableAmount'] = $this->retractableAmount[$objectType->objectTypeID];
            }

            if (!empty($data)) {
                $objectTypeAction = new ObjectTypeAction([$objectType], 'update', [
                    'data' => [
                        'additionalData' => \serialize(\array_merge($objectType->additionalData, $data)),
                    ],
                ]);
                $objectTypeAction->executeAction();
            }
        }

        $this->saved();
    }

    /**
     * @inheritdoc
     */
    public function saved()
    {
        parent::saved();

        WCF::getTPL()->assign('success', true);
    }

    /**
     * @inheritdoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'objectTypes' => $this->objectTypes,
            'objectTypeCategories' => $this->objectTypesCategories,
            'objectTypesByCategory' => $this->objectTypesByCategory,
            'amount' => $this->amount,
            'retractableAmount' => $this->retractableAmount,
        ]);
    }
}
