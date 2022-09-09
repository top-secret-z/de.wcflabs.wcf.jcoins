<?php
namespace wcf\data\jcoins\statement;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\WCF;

/**
 * Global JCoins statement list.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class GlobaleJCoinsStatementList extends JCoinsStatementList {
	/**
	 * @inheritDoc
	 */
	public $className = GlobaleJCoinsStatement::class;
	
	/**
	 * Creates a new GlobaleJCoinsStatementList object.
	 */
	public function __construct() {
		parent::__construct();
		
		$this->sqlSelects = 'user_table.username';
		$this->sqlJoins = " LEFT JOIN wcf".WCF_N."_user user_table ON (user_table.userID = jcoins_statement.userID)";
	}
	
	/**
	 * Returns a list of available object types.
	 */
	public function getAvailableObjectTypes() {
		$types = [];
		$sql = "SELECT	DISTINCT objectTypeID
				FROM	wcf".WCF_N."_jcoins_statement";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			if ($row['objectTypeID']) {
				$objectType = ObjectTypeCache::getInstance()->getObjectType($row['objectTypeID']);
				$types[$row['objectTypeID']] = WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.title.' . $objectType->objectType);
			}
		}
		ksort($types);
		
		return $types;
	}
}
