<?php
namespace wcf\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * Exports user data iwa Gdpr.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsGdprExportListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// exclude one option and add balance data in user
		$eventObj->skipUserOptions[] = 'jcoinsShowBadge';
		$eventObj->exportUserProperties[] = 'jCoinsAmount';
		
		$eventObj->data['de.wcflabs.wcf.jcoins'] = [
				'jCoinsTransactionLog' => $this->dumpTable('wcf' . WCF_N . '_purchasable_jcoins_transaction_log', 'userID', $eventObj->user->userID),
		];
	}
	
	/**
	 * dump table copied from action and modified
	 */
	protected function dumpTable($tableName, $userIDColumn, $userID) {
		$sql = "SELECT	*
				FROM	${tableName}
				WHERE	${userIDColumn} = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$userID]);
		
		$data = [];
		while ($row = $statement->fetchArray()) {
			$data[] = $row;
		}
		
		return $data;
	}
}
