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
namespace wcf\system\event\listener;

use wcf\data\conversation\Conversation;
use wcf\form\ConversationMessageAddForm;
use wcf\page\ConversationPage;
use wcf\system\exception\NamedUserException;
use wcf\system\message\QuickReplyManager;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Checks whether the user has enougth JCoins to answer the conversation.
 */
class JCoinsConversationMessageAddFormListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS || JCOINS_ALLOW_NEGATIVE) {
            return;
        }

        if (!WCF::getSession()->getPermission('user.jcoins.canEarn') || !WCF::getSession()->getPermission('user.jcoins.canUse')) {
            return;
        }

        $statement = UserJCoinsStatementHandler::getInstance()->getStatementProcessorInstance('de.wcflabs.jcoins.statement.conversation.answer');
        if ($statement->calculateAmount() < 0 && ($statement->calculateAmount() * -1) > WCF::getUser()->jCoinsAmount) {
            if ($eventObj instanceof ConversationMessageAddForm) {
                throw new NamedUserException(WCF::getLanguage()->get('wcf.jcoins.amount.tooLow'));
            } elseif ($eventObj instanceof ConversationPage) {
                WCF::getTPL()->assign([
                    'hasEnougthJCoins' => !($statement->calculateAmount() < 0 && ($statement->calculateAmount() * -1) > WCF::getUser()->jCoinsAmount),
                ]);
            } elseif ($eventObj instanceof QuickReplyManager && $eventObj->container instanceof Conversation) {
                throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.jcoins.amount.tooLow'));
            }
        }
    }
}
