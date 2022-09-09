<?php
namespace wcf\system\bulk\processing\user;
use wcf\data\user\UserList;
use wcf\data\DatabaseObjectList;
use wcf\system\worker\JCoinsResetWorker;
use wcf\system\WCF;

/**
 * Reset JCoins bulk action.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class ResetJCoinsUserBulkProcessingAction extends AbstractUserBulkProcessingAction {
	/**
	 * email text
	 * @var	string
	 */
	public $resetID = 0;
	
	/**
	 * @inheritdoc
	 */
	public function executeAction(DatabaseObjectList $objectList) {
		if (!($objectList instanceof UserList)) return;
		
		if (count($objectList)) {
			// save config in session
			$data = WCF::getSession()->getVar(JCoinsResetWorker::DATA_VARIABLE_NAME);
			if ($data === null) $data = [];
			$this->resetID = count($data) + 1;
			$data[$this->resetID] = [
					'userIDs' => $objectList->getObjectIDs()
			];
			WCF::getSession()->register(JCoinsResetWorker::DATA_VARIABLE_NAME, $data);
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function getHTML() {
		return WCF::getTPL()->fetch('resetJCoinsUserBulkProcessing', 'wcf', [
				'resetID' => $this->resetID
		]);
	}
}
