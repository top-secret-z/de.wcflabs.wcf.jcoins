<?php
namespace wcf\system\event\listener;
use wcf\data\conversation\Conversation;
use wcf\data\conversation\message\ConversationMessage;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for new conversations.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsConversationAddListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_CONVERSATION || !MODULE_JCOINS) return;
		
		switch ($eventObj->getActionName()) {
			case 'create':
				if (!WCF::getUser()->userID) return;
				
				$params = $eventObj->getParameters();
				if (isset($params['data']['isDraft']) && $params['data']['isDraft']) return;
				
				$returnValues = $eventObj->getReturnValues();
				UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.conversation.start', $returnValues['returnValues'], ['userID' => $returnValues['returnValues']->userID]);
				break;
				
			case 'update':
				$params = $eventObj->getParameters();
				if (isset($params['data']['isDraft']) && $params['data']['isDraft']) return;
				
				$conversations = $eventObj->getObjects();
				foreach ($conversations as $conversation) {
					$conversation = $conversation->getDecoratedObject($conversation);
					if ($conversation->isDraft) {
						UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.conversation.start', $conversation, ['userID' => $conversation->userID]);
						
						$ids = [];
						$sql = "SELECT	messageID
								FROM	wcf".WCF_N."_conversation_message
								WHERE	conversationID = ? AND userID = ?";
						$statement = WCF::getDB()->prepareStatement($sql);
						$statement->execute([$conversation->conversationID, WCF::getUser()->userID]);
						while ($row = $statement->fetchArray()) {
							$ids[] = $row['messageID'];
						}
						
						if (count($ids) > 1) {
							foreach ($ids as $key => $messageID) {
								if (!$key) continue;
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
						if ($conversation->time < JCOINS_INSTALL_CONV_DATE) continue;
						if ($conversation->isDraft) continue;
						
						if ($conversation->userID == $userID) {
							$starter = 1;
							UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.conversation.start', null, ['userID' => $userID]);
						}
						
						$ids = [];
						$sql = "SELECT	messageID
								FROM	wcf".WCF_N."_conversation_message
								WHERE	conversationID = ? AND userID = ?";
						$statement = WCF::getDB()->prepareStatement($sql);
						$statement->execute([$conversation->conversationID, $userID]);
						while ($row = $statement->fetchArray()) {
							$ids[] = $row['messageID'];
						}
						
						if (count($ids) > 0) {
							foreach ($ids as $key => $messageID) {
								if (!$key && $starter) continue;
								
								UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.conversation.answer', null, ['userID' => $userID]);
							}
						}
					}
				}
				
				break;
		}
	}
}
