<?php
namespace wcf\acp\page;
use wcf\page\SortablePage;

/**
 * Provides the purchasable jcoins list.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsListPage extends SortablePage {
	/**
	 * @inheritdoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.purchasableJCoins.list';

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
	public $defaultSortField = 'purchasableJCoinsID';

	/**
	 * @inheritdoc
	 */
	public $objectListClassName = 'wcf\data\purchasable\jcoins\PurchasableJCoinsList';

	/**
	 * @inheritdoc
	 */
	public $validSortFields = ['purchasableJCoinsID', 'title', 'description', 'cost', 'jCoins', 'currency'];
}
