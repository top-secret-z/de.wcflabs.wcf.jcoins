<?php
namespace wcf\system\bulk\processing\user;
use wcf\data\user\UserList;
use wcf\data\DatabaseObjectList;
use wcf\system\exception\UserInputException;
use wcf\system\worker\JCoinsTransferWorker;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Transfer JCoins bulk action.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class TransferJCoinsUserBulkProcessingAction extends AbstractUserBulkProcessingAction {
	/**
	 * amount
	 */
	public $amount = 0;
	
	/**
	 * transfer id
	 */
	public $transferID = 0;
	
	/**
	 * the reason for the transfer
	 */
	public $reason = '';
	
	/**
	 * @inheritdoc
	 */
	public function executeAction(DatabaseObjectList $objectList) {
		if (!($objectList instanceof UserList)) return;
		
		if (count($objectList)) {
			// save config in session
			$data = WCF::getSession()->getVar(JCoinsTransferWorker::DATA_VARIABLE_NAME);
			if ($data === null) $data = [];
			$this->transferID = count($data) + 1;
			$data[$this->transferID] = [
					'amount' => $this->amount,
					'reason' => $this->reason,
					'userIDs' => $objectList->getObjectIDs()
			];
			WCF::getSession()->register(JCoinsTransferWorker::DATA_VARIABLE_NAME, $data);
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function getHTML() {
		return WCF::getTPL()->fetch('transferJCoinsUserBulkProcessing', 'wcf', [
				'amount' => $this->amount,
				'reason' => $this->reason,
				'transferID' => $this->transferID
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function readFormParameters() {
		if (isset($_POST['amount'])) $this->amount = intval($_POST['amount']);
		if (isset($_POST['reason'])) $this->reason = StringUtil::trim($_POST['reason']);
	}
	
	/**
	 * @inheritdoc
	 */
	public function validate() {
		if (empty($this->reason)) {
			throw new UserInputException('reason');
		}
		
		if ($this->amount == 0) {
			throw new UserInputException('amount');
		}
	}
}
