<?php
namespace wcf\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * Adds JCoins search fields to MembersList.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsMembersListPageListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// only if can view
		if (MODULE_JCOINS && WCF::getSession()->getPermission('user.jcoins.canSee')) {
			$eventObj->validSortFields[] = 'jCoinsAmount';
		}
	}
}
