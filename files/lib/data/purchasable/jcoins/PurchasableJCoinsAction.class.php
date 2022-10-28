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
namespace wcf\data\purchasable\jcoins;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;

/**
 * DatabaseObject-related functions for JCoins statements.
 */
class PurchasableJCoinsAction extends AbstractDatabaseObjectAction implements IToggleAction
{
    /**
     * @inheritdoc
     */
    protected $permissionsDelete = ['admin.purchasableJCoins.canManage'];

    /**
     * @inheritdoc
     */
    protected $permissionsUpdate = ['admin.purchasableJCoins.canManage'];

    /**
     * @inheritdoc
     */
    protected $requireACP = ['create', 'delete', 'toggle', 'update'];

    /**
     * @inheritdoc
     */
    public function validateToggle()
    {
        parent::validateUpdate();
    }

    /**
     * @inheritdoc
     */
    public function toggle()
    {
        foreach ($this->objects as $object) {
            $object->update([
                'isDisabled' => $object->isDisabled ? 0 : 1,
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        $showOrder = 0;
        if (isset($this->parameters['data']['showOrder'])) {
            $showOrder = $this->parameters['data']['showOrder'];
            unset($this->parameters['data']['showOrder']);
        }

        $object = parent::create();
        $editor = new PurchasableJCoinsEditor($object);
        $editor->setShowOrder($showOrder);

        return new PurchasableJCoins($object->purchasableJCoinsID);
    }

    /**
     * @inheritdoc
     */
    public function update()
    {
        parent::update();

        if (\count($this->objects) == 1 && isset($this->parameters['data']['showOrder']) && $this->parameters['data']['showOrder'] != \reset($this->objects)->showOrder) {
            \reset($this->objects)->setShowOrder($this->parameters['data']['showOrder']);
        }
    }
}
