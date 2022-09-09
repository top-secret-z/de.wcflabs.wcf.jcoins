<?php
namespace wcf\data\purchasable\jcoins;
use wcf\system\cache\builder\PurchasableJCoinsCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Represents a purchasable JCoins entry cache.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsCache extends SingletonFactory {
	/**
	 * cached purchasable JCoins
	 */
	protected $cachedPurchasableJCoins = [];

	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->cachedPurchasableJCoins = PurchasableJCoinsCacheBuilder::getInstance()->getData();
		
		$orphanedPurchasableJCoins = []; 
		foreach ($this->cachedPurchasableJCoins as $id => $purchasableJCoins) {
			if ($purchasableJCoins->availableUntil != 0 && $purchasableJCoins->availableUntil < TIME_NOW) {
				$orphanedPurchasableJCoins[] = $purchasableJCoins;
				
				unset($this->cachedPurchasableJCoins[$id]);
			}
		}
		
		if (!empty($orphanedPurchasableJCoins)) {
			$action = new PurchasableJCoinsAction($orphanedPurchasableJCoins, 'update', [
				'data' => [
					'isDisabled' => 1
				]
			]); 
			$action->executeAction(); 
		}
	}
	
	/**
	 * Returns all purchasable JCoins.
	 */
	public function getPurchasableJCoins() {
		return $this->cachedPurchasableJCoins;
	}
}
