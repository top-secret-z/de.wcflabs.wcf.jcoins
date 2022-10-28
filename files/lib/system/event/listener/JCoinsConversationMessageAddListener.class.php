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
use wcf\data\conversation\message\ConversationMessage;
use wcf\data\conversation\message\ConversationMessageAction;
use wcf\system\message\QuickReplyManager;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add Jcoins for new conversation messages.
 */
class JCoinsConversationMessageAddListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_CONVERSATION || !MODULE_JCOINS) {
            return;
        }

        if ($eventObj instanceof ConversationMessageAction && $eventObj->getActionName() == 'create') {
            if (!WCF::getUser()->userID) {
                return;
            }

            $parameters = $eventObj->getParameters();
            if (isset($parameters['isFirstPost'])) {
                return;
            }

            $returnValues = $eventObj->getReturnValues();
            $conversationMessage = $returnValues['returnValues'];

            UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.conversation.answer', $conversationMessage);
        } elseif ($eventObj instanceof QuickReplyManager && $parameters['message'] instanceof ConversationMessage) {
            if (!WCF::getUser()->userID) {
                return;
            }

            $conversation = new Conversation($parameters['message']->conversationID);
            if ($conversation->isDraft) {
                return;
            }

            UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.conversation.answer', $parameters['message']);
        }

        if ($eventObj instanceof ConversationMessageAction && $eventObj->getActionName() == 'delete') {
            $messages = $eventObj->getObjects();
            $userID = WCF::getUser()->userID;

            $conversationID = 0;
            foreach ($messages as $message) {
                // skip first
                if ($conversationID != $message->conversationID) {
                    $conversationID = $message->conversationID;
                    continue;
                }

                if ($message->userID == $userID && $message->time > JCOINS_INSTALL_CONV_DATE) {
                    UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.conversation.answer', null, ['userID' => $message->userID]);
                }
            }
        }
    }
}
