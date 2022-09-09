<?php
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
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsConversationMessageAddFormListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS || JCOINS_ALLOW_NEGATIVE) return;
		
		if (!WCF::getSession()->getPermission('user.jcoins.canEarn') || !WCF::getSession()->getPermission('user.jcoins.canUse')) return;
		
		$statement = UserJCoinsStatementHandler::getInstance()->getStatementProcessorInstance('de.wcflabs.jcoins.statement.conversation.answer');
		if ($statement->calculateAmount() < 0 && ($statement->calculateAmount() * -1) > WCF::getUser()->jCoinsAmount) {
			if ($eventObj instanceof ConversationMessageAddForm) {
				throw new NamedUserException(WCF::getLanguage()->get('wcf.jcoins.amount.tooLow'));
			}
			else if ($eventObj instanceof ConversationPage) {
				WCF::getTPL()->assign([
						'hasEnougthJCoins' => !($statement->calculateAmount() < 0 && ($statement->calculateAmount() * -1) > WCF::getUser()->jCoinsAmount)
				]);
			}
			else if ($eventObj instanceof QuickReplyManager && $eventObj->container instanceof Conversation) {
				throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.jcoins.amount.tooLow'));
			}
		}
	}
}
