<?php
namespace wcf\system\event\listener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for trophies.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsUserTrophyActionListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS || !MODULE_TROPHY) return;
		
		// assign JCoins
		switch ($eventObj->getActionName()) {
			case 'create':
				$returnValues = $eventObj->getReturnValues();
				$userTrophy = $returnValues['returnValues'];
				$trophy = $userTrophy->getTrophy();
				
				if (!$trophy->isDiabled) {
					UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.trophy', $trophy, ['userID' => $userTrophy->userID]);
				}
				break;
				
			case 'delete':
				foreach ($eventObj->getObjects() as $object) {
					$userTrophy = $object->getDecoratedObject();
					$trophy = $userTrophy->getTrophy();
					
					if (!$trophy->isDiabled) {
						UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.trophy', $trophy, ['userID' => $userTrophy->userID]);
					}
				}
				break;
		}
	}
}
