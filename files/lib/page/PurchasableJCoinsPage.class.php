<?php
namespace wcf\page;
use wcf\data\purchasable\jcoins\PurchasableJCoinsCache;
use wcf\system\WCF;

/**
 * Purchasable JCoins Page.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsPage extends AbstractPage {
	/**
	 * @inheritdoc
	 */
	public $loginRequired = true;
	
	/**
	 * @inheritdoc
	 */
	public $neededModules = ['MODULE_PURCHASABLE_JCOINS', 'MODULE_JCOINS'];
	
	/**
	 * @inheritdoc
	 */
	public $neededPermissions = ['user.jcoins.canUse'];
	
	/**
	 * list of available purchasable JCoins
	 */
	public $purchasableJCoins = [];
	
	/**
	 * @inheritdoc
	 */
	public function readData() {
		parent::readData();
		
		// get available subscriptions
		$this->purchasableJCoins = PurchasableJCoinsCache::getInstance()->getPurchasableJCoins();
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'purchasableJCoins' => $this->purchasableJCoins
		]);
	}
}
