<?php
namespace wcf\data\jcoins\statement;
use wcf\data\DatabaseObjectList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\WCF;

/**
 * JCoins statement list.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsStatementList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsStatement::class;
	
	/**
	 * Returns a list of available object types.
	 */
	public function getAvailableObjectTypes() {
		$types = [];
		$sql = "SELECT	DISTINCT objectTypeID
				FROM	wcf".WCF_N."_jcoins_statement
				WHERE	userID = ? AND isTrashed = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([WCF::getUser()->userID, 0]);
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
