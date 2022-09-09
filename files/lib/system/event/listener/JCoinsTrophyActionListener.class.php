<?php
namespace wcf\system\event\listener;
use wcf\data\user\trophy\UserTrophyList;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Add JCoins for trophies when enabling / disabling trophies.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsTrophyActionListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS || !MODULE_TROPHY) return;
		
		// only toggle
		if ($eventObj->getActionName() != 'toggle') return;
		
		foreach($eventObj->getObjects() as $object) {
			$trophy = $object->getDecoratedObject();
			
			// get user trophies
			$userTrophyList = new UserTrophyList();
			$userTrophyList->getConditionBuilder()->add('trophyID = ?', [$trophy->trophyID]);
			$userTrophyList->readObjects();
			$userTrophies = $userTrophyList->getObjects();
			if (empty($userTrophies)) continue;
			
			// step through user trophies and assign JCoins
			foreach($userTrophies as $userTrophy) {
				if ($trophy->isDisabled) {
					UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.trophy', $trophy, ['userID' => $userTrophy->userID]);
				}
				if (!$trophy->isDisabled) {
					UserJCoinsStatementHandler::getInstance()->revoke('de.wcflabs.jcoins.statement.trophy', $trophy, ['userID' => $userTrophy->userID]);
				}
			}
		}
	}
}
