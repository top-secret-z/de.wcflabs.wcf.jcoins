<?php
namespace wcf\system\event\listener;
use wcf\data\user\UserAction;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for a login.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsLoginListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// module and user required
		if (!MODULE_JCOINS) return;
		if (!WCF::getUser()->userID) return;
		
		if ($eventObj->getUser()->jCoinsLastDailyBonus < date('Ymd')) {
			UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.login', $eventObj->getUser());
			
			$userAction = new UserAction([$eventObj->getUser()], 'update', [
					'data' => [
							'jCoinsLastDailyBonus' => date('Ymd')
					]
			]);
			$userAction->executeAction();
		}
	}
}
