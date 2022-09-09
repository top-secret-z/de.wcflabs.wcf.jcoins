<?php
namespace wcf\system\event\listener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for new comments.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsCommentResponseActionListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS) return;
		
		// validation (leave ufn)
		if ($eventName == 'validateAction') {
			return true;
		}
		
		// assign JCoins 
		// some event might not be triggered here, but leave it ufn
		switch ($eventObj->getActionName()) {
			case 'triggerPublication':
				foreach ($eventObj->getObjects() as $object) {
					if ($object->userID) {
						UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.comment.response', $object->getDecoratedObject());
					}
				}
				break;
				
				// 'enable' calls triggerPublication
				
			case 'disable':
				foreach ($eventObj->getObjects() as $object) {
					if ($object->userID) {
						UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.comment.response', $object->getDecoratedObject());
					}
				}
				break;
				
			case 'delete':
				foreach ($eventObj->getObjects() as $object) {
					if (!$object->isDisabled && $object->userID) {
						UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.comment.response', $object->getDecoratedObject());
					}
				}
				break;
		}
	}
}
