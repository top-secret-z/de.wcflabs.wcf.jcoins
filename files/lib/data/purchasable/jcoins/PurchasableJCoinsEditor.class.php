<?php
namespace wcf\data\purchasable\jcoins;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\PurchasableJCoinsCacheBuilder;
use wcf\system\WCF;

/**
 * Object Editor for purchasable JCoins entries.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @inheritdoc
	 */
	protected static $baseClass = PurchasableJCoins::class;
	
	/**
	 * Sets the show order of the offer.
	 */
	public function setShowOrder($showOrder = 0) {
		$sql = "SELECT	MAX(showOrder)
			FROM	wcf".WCF_N."_purchasable_jcoins";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$maxShowOrder = $statement->fetchColumn();
		if (!$maxShowOrder) $maxShowOrder = 0;
		
		if (!$showOrder || $showOrder > $maxShowOrder) {
			$newShowOrder = $maxShowOrder + 1;
		}
		else {
			// shift other subscriptions
			$sql = "UPDATE	wcf".WCF_N."_purchasable_jcoins
				SET	showOrder = showOrder + 1
				WHERE	showOrder >= ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([
			    $showOrder
			]);
			
			$newShowOrder = $showOrder;
		}
		
		$this->update([
		    'showOrder' => $newShowOrder
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public static function resetCache() {
		PurchasableJCoinsCacheBuilder::getInstance()->reset(); 
	}
}
