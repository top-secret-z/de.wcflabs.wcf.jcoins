<?php
namespace wcf\data\purchasable\jcoins\transaction\log;
use wcf\data\DatabaseObjectEditor;

/**
 * Object Editor for purchasable JCoins transaction log entries.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsTransactionLogEditor extends DatabaseObjectEditor {
	/**
	 * @inheritdoc
	 */
	protected static $baseClass = PurchasableJCoinsTransactionLog::class;
}
