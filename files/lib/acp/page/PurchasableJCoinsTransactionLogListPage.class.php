<?php
namespace wcf\acp\page;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Provides the purchasable jcoins transcation list.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsTransactionLogListPage extends SortablePage {
	/**
	 * @inheritdoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.purchasableJCoins.transactionLog.list';
	
	/**
	 * @inheritdoc
	 */
	public $neededModules = ['MODULE_JCOINS', 'MODULE_PURCHASABLE_JCOINS'];
	
	/**
	 * @inheritdoc
	 */
	public $neededPermissions = ['admin.purchasableJCoins.canManage'];
	
	/**
	 * @inheritdoc
	 */
	public $defaultSortField = 'logTime';
	
	/**
	 * @inheritdoc
	 */
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @inheritdoc
	 */
	public $objectListClassName = 'wcf\data\purchasable\jcoins\transaction\log\PurchasableJCoinsTransactionLogList';
	
	/**
	 * @inheritdoc
	 */
	public $validSortFields = ['logID', 'logMessage', 'purchasableJCoinsID', 'paymentMethodObjectTypeID', 'transactionID', 'logTime'];
	
	/**
	 * transaction id
	 */
	public $transactionID = '';
	
	/**
	 * username
	 */
	public $username = '';
	
	/**
	 * @inheritdoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['transactionID'])) $this->transactionID = StringUtil::trim($_REQUEST['transactionID']);
		if (isset($_REQUEST['username'])) $this->username = StringUtil::trim($_REQUEST['username']);
	}
	
	/**
	 * @inheritdoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		if ($this->transactionID) {
			$this->objectList->getConditionBuilder()->add('purchasable_jcoins_transaction_log.transactionID LIKE ?', ['%' . $this->transactionID . '%']);
		}
		
		if ($this->username) {
			$this->objectList->getConditionBuilder()->add('purchasable_jcoins_transaction_log.userID IN (SELECT userID FROM wcf' . WCF_N . '_user WHERE username LIKE ?)', ['%' . $this->username . '%']);
		}
		
		$this->objectList->sqlSelects = 'user_table.username, purchasable_jcoins.title';
		$this->objectList->sqlJoins = "LEFT JOIN wcf" . WCF_N . "_user user_table ON (user_table.userID = purchasable_jcoins_transaction_log.userID)";
		$this->objectList->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_purchasable_jcoins purchasable_jcoins ON (purchasable_jcoins.purchasableJCoinsID = purchasable_jcoins_transaction_log.purchasableJCoinsID)";
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
			'transactionID' => $this->transactionID,
			'username' => $this->username
		]);
	}
}
