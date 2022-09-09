<?php
namespace wcf\system\cache\builder;
use wcf\data\purchasable\jcoins\PurchasableJCoinsList;

/**
 * Builds the cache for purchasable jcoins.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritdoc
	 */
	public function rebuild(array $parameters) {
		$list = new PurchasableJCoinsList();
		$list->getConditionBuilder()->add("isDisabled = ?", [0]);
		$list->sqlOrderBy = 'showOrder ASC';
		$list->readObjects();
		
		return $list->getObjects();
	}
}
