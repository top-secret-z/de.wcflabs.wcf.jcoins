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
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for new conversations.
 */
class JCoinsConversationAddListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_CONVERSATION || !MODULE_JCOINS) {
            return;
        }

        switch ($eventObj->getActionName()) {
            case 'create':
                if (!WCF::getUser()->userID) {
                    return;
                }

                $params = $eventObj->getParameters();
                if (isset($params['data']['isDraft']) && $params['data']['isDraft']) {
                    return;
                }

                $returnValues = $eventObj->getReturnValues();
                UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.conversation.start', $returnValues['returnValues'], ['userID' => $returnValues['returnValues']->userID]);
                break;

            case 'update':
                $params = $eventObj->getParameters();
                if (isset($params['data']['isDraft']) && $params['data']['isDraft']) {
                    return;
                }

                $conversations = $eventObj->getObjects();
                foreach ($conversations as $conversation) {
                    $conversation = $conversation->getDecoratedObject($conversation);
                    if ($conversation->isDraft) {
                        UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.conversation.start', $conversation, ['userID' => $conversation->userID]);

                        $ids = [];
                        $sql = "SELECT    messageID
                                FROM    wcf" . WCF_N . "_conversation_message
                                WHERE    conversationID = ? AND userID = ?";
                        $statement = WCF::getDB()->prepareStatement($sql);
                        $statement->execute([$conversation->conversationID, WCF::getUser()->userID]);
                        while ($row = $statement->fetchArray()) {
                            $ids[] = $row['messageID'];
                        }

                        if (\count($ids) > 1) {
                            foreach ($ids as $key => $messageID) {
                                if (!$key) {
                                    continue;
                                }
                                $message = new ConversationMessage($messageID);
                                if ($message->messageID) {
                                    UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.conversation.answer', $message);
                                }
                            }
                        }
                    }
                }

                break;

            case 'hideConversation':
                $parameters = $eventObj->getParameters();

                if (isset($parameters['hideConversation']) && $parameters['hideConversation'] == Conversation::STATE_LEFT) {
                    $userID = WCF::getUser()->userID;
                    $starter = 0;

                    $conversations = $eventObj->getObjects();
                    foreach ($conversations as $conversation) {
                        if ($conversation->time < JCOINS_INSTALL_CONV_DATE) {
                            continue;
                        }
                        if ($conversation->isDraft) {
                            continue;
                        }

                        if ($conversation->userID == $userID) {
                            $starter = 1;
                            UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.conversation.start', null, ['userID' => $userID]);
                        }

                        $ids = [];
                        $sql = "SELECT    messageID
                                FROM    wcf" . WCF_N . "_conversation_message
                                WHERE    conversationID = ? AND userID = ?";
                        $statement = WCF::getDB()->prepareStatement($sql);
                        $statement->execute([$conversation->conversationID, $userID]);
                        while ($row = $statement->fetchArray()) {
                            $ids[] = $row['messageID'];
                        }

                        if (\count($ids) > 0) {
                            foreach ($ids as $key => $messageID) {
                                if (!$key && $starter) {
                                    continue;
                                }

                                UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.conversation.answer', null, ['userID' => $userID]);
                            }
                        }
                    }
                }

                break;
        }
    }
}
