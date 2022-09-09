<?php
namespace wcf\acp\page;
use wcf\data\purchasable\jcoins\transaction\log\PurchasableJCoinsTransactionLog;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Provides the purchasable jcoins transcation log page.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsTransactionLogPage extends AbstractPage {
	/**
	 * @inheritdoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.purchasableJCoins';
	
	/**
	 * @inheritdoc
	 */
	public $neededModules = ['MODULE_JCOINS', 'MODULE_PURCHASABLE_JCOINS'];
	
	/**
	 * @inheritdoc
	 */
	public $neededPermissions = ['admin.purchasableJCoins.canManage'];
	
	/**
	 * log entry id
	 */
	public $logID = 0;
	
	/**
	 * log entry object
	 */
	public $log = null;
	
	/**
	 * @inheritdoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) {
			$this->logID = intval($_REQUEST['id']);
		}
		
		$this->log = new PurchasableJCoinsTransactionLog($this->logID);
		if (!$this->log->logID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
			'logID' => $this->logID,
			'log' => $this->log
		]);
	}
}
