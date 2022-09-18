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
namespace wcf\system\user\notification\event;

use wcf\system\email\Email;
use wcf\system\request\LinkHandler;

/**
 * The user JCoins statement handler, which should be used to create statements.
 */
class JCoinsTransferNotificationEvent extends AbstractUserNotificationEvent
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->getLanguage()->get('wcf.user.notification.jcoins.transfer.title');
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->getLanguage()->getDynamicVariable('wcf.user.notification.jcoins.transfer.message', [
            'statement' => $this->userNotificationObject,
            'author' => $this->author,
        ]);
    }

    /**
     * @inheritdoc
     */
/*    public function getEmailMessage($notificationType = 'instant') {
        return $this->getLanguage()->getDynamicVariable('wcf.user.notification.jcoins.transfer.mail', [
                'statement' => $this->userNotificationObject,
                'author' => $this->author
        ]);
    }
*/
    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant')
    {
        $messageID = '<de.wcflabs.jcoins.statement/' . $this->getUserNotificationObject()->statementID . '@' . Email::getHost() . '>';

        return [
            'template' => 'email_notification_jCoinsTransfer',
            'application' => 'wcf',
            'in-reply-to' => [$messageID],
            'references' => [
                $messageID,
            ],
            'variables' => [
                'languageVariablePrefix' => 'wcf.user.notification.jcoins.transfer',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLink()
    {
        return LinkHandler::getInstance()->getLink('JCoinsStatementList');
    }
}
