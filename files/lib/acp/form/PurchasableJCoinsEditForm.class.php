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

use wcf\data\purchasable\jcoins\PurchasableJCoins;
use wcf\data\purchasable\jcoins\PurchasableJCoinsAction;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Provides the purchasable jcoins edit form.
 */
class PurchasableJCoinsEditForm extends PurchasableJCoinsAddForm
{
    /**
     * @inheritdoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.jcoins';

    /**
     * object id
     */
    public $objectID = 0;

    /**
     * Purchasable jcoins object
     */
    public $object;

    /**
     * @inheritdoc
     */
    public function readParameters()
    {
        if (isset($_REQUEST['id'])) {
            $this->objectID = \intval($_REQUEST['id']);
        }
        $this->object = new PurchasableJCoins($this->objectID);
        if (!$this->object->getObjectID()) {
            throw new IllegalLinkException();
        }

        parent::readParameters();
    }

    /**
     * @inheritdoc
     */
    public function readData()
    {
        parent::readData();

        if (empty($_POST)) {
            I18nHandler::getInstance()->setOptions('description', 1, $this->object->description, 'wcf.acp.purchasableJCoins.offer\d+.description');
            I18nHandler::getInstance()->setOptions('title', 1, $this->object->title, 'wcf.acp.purchasableJCoins.offer\d+.title');

            $this->isDisabled = $this->object->isDisabled;
            $this->showOrder = $this->object->showOrder;
            $this->cost = $this->object->cost;
            $this->currency = $this->object->currency;
            $this->jCoins = $this->object->jCoins;
            $this->useHTML = $this->object->useHTML;
            $this->availableUntil = ($this->object->availableUntil) ? \date('Y-m-d h:i', $this->object->availableUntil) : '';
        }
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        AbstractForm::save();

        $this->description = 'wcf.acp.purchasableJCoins.offer' . $this->object->getObjectID() . '.description';
        if (I18nHandler::getInstance()->isPlainValue('description')) {
            I18nHandler::getInstance()->remove($this->description);
            $this->description = I18nHandler::getInstance()->getValue('description');
        } else {
            I18nHandler::getInstance()->save('description', $this->description, 'wcf.acp.purchasableJCoins');
        }

        $this->title = 'wcf.acp.purchasableJCoins.offer' . $this->object->getObjectID() . '.title';
        if (I18nHandler::getInstance()->isPlainValue('title')) {
            I18nHandler::getInstance()->remove($this->title);
            $this->title = I18nHandler::getInstance()->getValue('title');
        } else {
            I18nHandler::getInstance()->save('title', $this->title, 'wcf.paidSubscription');
        }

        $this->objectAction = new PurchasableJCoinsAction([$this->object], 'update', ['data' => \array_merge($this->additionalFields, [
            'title' => $this->title,
            'description' => $this->description,
            'isDisabled' => $this->isDisabled,
            'showOrder' => $this->showOrder,
            'cost' => $this->cost,
            'currency' => $this->currency,
            'jCoins' => $this->jCoins,
            'useHTML' => $this->useHTML,
            'availableUntil' => (!empty($this->availableUntil)) ? \strtotime($this->availableUntil) : 0,
        ])]);
        $this->objectAction->executeAction();

        $this->saved();

        // show success
        WCF::getTPL()->assign([
            'success' => true,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        I18nHandler::getInstance()->assignVariables(!empty($_POST));

        WCF::getTPL()->assign([
            'action' => 'edit',
            'object' => $this->object,
            'purchasableJCoinsID' => $this->objectID,
        ]);
    }
}
