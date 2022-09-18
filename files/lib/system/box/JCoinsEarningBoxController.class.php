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

use wcf\data\object\type\ObjectTypeCache;
use wcf\system\WCF;

/**
 * JCoins earning box controller.
 */
class JCoinsEarningBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];

    /**
     * The definition name for statement objects.
     */
    const DEFINITION_NAME = 'de.wcflabs.jcoins.statement.object';

    /**
     * JCoins object type categories
     */
    public $objectTypesCategories = [];

    /**
     * @inheritDoc
     */
    protected function loadContent()
    {
        if (!MODULE_JCOINS) {
            return;
        }

        $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes(self::DEFINITION_NAME);
        foreach ($objectTypes as $id => $objectType) {
            if ($objectType->editable === "0") {
                unset($objectTypes[$id]);
            } elseif ($objectType->amount === 0 || ($objectType->amount === null && $objectType->defaultAmount == 0)) {
                unset($objectTypes[$id]);
            }
        }

        foreach ($objectTypes as $objectType) {
            if (!\array_key_exists($objectType->category, $this->objectTypesCategories)) {
                $this->objectTypesCategories[$objectType->category] = [];
            }

            $this->objectTypesCategories[$objectType->category][] = $objectType;
        }

        if (!\count($this->objectTypesCategories)) {
            return '';
        }

        WCF::getTPL()->assign([
            'objectTypesCategories' => $this->objectTypesCategories,
        ]);

        $this->content = WCF::getTPL()->fetch('boxJCoinsEarningOverview');
    }
}
