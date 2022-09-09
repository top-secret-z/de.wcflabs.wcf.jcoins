<?php
namespace wcf\system\event\listener;
use wcf\system\exception\NamedUserException;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for new comments.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsCommentActionListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS) return;
		
		// check sufficient JCoins for comment and comment response creation
		if ($eventName == 'validateAction') {
			
			if (JCOINS_ALLOW_NEGATIVE) return;
			if (!WCF::getSession()->getPermission('user.jcoins.canEarn') || !WCF::getSession()->getPermission('user.jcoins.canUse')) return;
			
			switch ($eventObj->getActionName()) {
				case 'addComment':
					$statement = UserJCoinsStatementHandler::getInstance()->getStatementProcessorInstance('de.wcflabs.jcoins.statement.comment.comment');
					break;
				case 'addResponse':
					$statement = UserJCoinsStatementHandler::getInstance()->getStatementProcessorInstance('de.wcflabs.jcoins.statement.comment.response');
					break;
				default:
					return true;
			}
			
			if ($statement->calculateAmount() < 0 && ($statement->calculateAmount() * -1) > WCF::getUser()->jCoinsAmount) {
				throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.jcoins.amount.tooLow'));
			}
			
			return true;
		}
		
		// assign JCoins
		switch ($eventObj->getActionName()) {
			case 'triggerPublication':
				foreach ($eventObj->getObjects() as $object) {
					if ($object->userID) {
						UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.comment.comment', $object->getDecoratedObject());
					}
				}
				break;
				
				// 'enable' calls triggerPublication
				
			case 'triggerPublicationResponse':
				$params = $eventObj->getParameters();
				$response = $params['responses'][0];
				
				if ($response->userID) {
					UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.comment.response', $response);
				}
				
			case 'disable':
				foreach ($eventObj->getObjects() as $object) {
					if ($object->userID) {
						UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.comment.comment', $object->getDecoratedObject());
					}
				}
				break;
				
			case 'delete':
				foreach ($eventObj->getObjects() as $object) {
					if (!$object->isDisabled && $object->userID) {
						UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.comment.comment', $object->getDecoratedObject());
					}
				}
				break;
		}
	}
}
