<?php
namespace wcf\system\event\listener;
use wcf\data\conversation\Conversation;
use wcf\data\conversation\message\ConversationMessage;
use wcf\data\conversation\message\ConversationMessageAction;
use wcf\system\message\QuickReplyManager;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add Jcoins for new conversation messages.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsConversationMessageAddListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_CONVERSATION || !MODULE_JCOINS) return;
		
		if ($eventObj instanceof ConversationMessageAction && $eventObj->getActionName() == 'create') {
			if (!WCF::getUser()->userID) return;
			
			$parameters = $eventObj->getParameters();
			if (isset($parameters['isFirstPost'])) return;
			
			$returnValues = $eventObj->getReturnValues();
			$conversationMessage = $returnValues['returnValues'];
			
			UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.conversation.answer', $conversationMessage);
		}
		else if ($eventObj instanceof QuickReplyManager && $parameters['message'] instanceof ConversationMessage) {
			if (!WCF::getUser()->userID) return;
			
			$conversation = new Conversation($parameters['message']->conversationID);
			if ($conversation->isDraft) return;
			
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
