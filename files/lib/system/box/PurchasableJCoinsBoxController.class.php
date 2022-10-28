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

use wcf\data\purchasable\jcoins\PurchasableJCoinsCache;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * JCoins purchasable JCoins box controller.
 */
class PurchasableJCoinsBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected static $supportedPositions = ['contentBottom', 'contentTop', 'sidebarLeft', 'sidebarRight'];

    /**
     * @inheritDoc
     */
    public function getLink()
    {
        return LinkHandler::getInstance()->getLink('PurchasableJCoins');
    }

    /**
     * @inheritDoc
     */
    public function hasLink()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function loadContent()
    {
        if (!MODULE_JCOINS) {
            return;
        }

        $purchasableJCoins = PurchasableJCoinsCache::getInstance()->getPurchasableJCoins();

        if (!\count($purchasableJCoins)) {
            return;
        }

        if ($this->getBox()->position == 'contentTop' || $this->getBox()->position == 'contentBottom') {
            $templateName = 'boxJCoinsPurchasableJCoins';
        } else {
            $templateName = 'boxJCoinsPurchasableJCoinsSidebar';
        }

        WCF::getTPL()->assign([
            'purchasableJCoins' => $purchasableJCoins,
        ]);

        $this->content = WCF::getTPL()->fetch($templateName);
    }
}
