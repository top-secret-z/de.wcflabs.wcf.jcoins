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
namespace wcf\system\user\notification\object;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\jcoins\statement\JCoinsStatement;
use wcf\data\user\User;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Transfer notification object
 */
class JCoinsTransferNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject
{
    /**
     * @inheritdoc
     */
    protected static $baseClass = JCoinsStatement::class;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        $user = new User($this->userID);
        if (!$user->userID) {
            return $user->getLanguage()->get('wcf.user.notification.jcoins.transfer.title');
        }

        return WCF::getLanguage()->get('wcf.user.notification.jcoins.transfer.title');
    }

    /**
     * @inheritdoc
     */
    public function getURL()
    {
        return LinkHandler::getInstance()->getLink('JCoinsStatementList');
    }

    /**
     * @inheritdoc
     */
    public function getAuthorID()
    {
        return $this->author;
    }
}
