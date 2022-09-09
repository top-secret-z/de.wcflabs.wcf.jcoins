<?php
namespace wcf\system\user\jcoins;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\DatabaseObject;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;

/**
 * The user JCoins statement handler, which should be used to create statements.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class UserJCoinsStatementHandler extends SingletonFactory {
	/**
	 * All JCoins object types.
	 */
	protected $objectTypes = [];
	
	/**
	 * All JCoins statement object types sorted by ID.
	 */
	protected $objectTypesByID = [];
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('de.wcflabs.jcoins.statement.object');
		
		foreach ($this->objectTypes as $oT) {
			$this->objectTypesByID[$oT->getObjectID()] = $oT;
		}
	}
	
	/**
	 * Creates a new statement with the given parameters. If you want to modify the author, use $parameter[author]
	 * if you want to modify the time, use $parameter[time].
	 */
	public function create($objectTypeName, DatabaseObject $object = null, array $parameters = []) {
		$newStatement = $this->getStatementProcessorInstance($objectTypeName);
		
		if ($object !== null) $newStatement->setObject($object);
		$newStatement->setParameters($parameters, true);
		
		// create the database object 
		$newStatement->save();
		
		return $newStatement;
	}
	
	/**
	 * Returns the object type by object-type-id.
	 */
	public function getObjectTypeByID($id) {
		return isset($this->objectTypesByID[$id]) ? $this->objectTypesByID[$id] : null;
	}
	
	/**
	 * Revokes a statement with the given parameters.
	 */
	public function revoke($objectTypeName, DatabaseObject $object = null, array $parameters = []) {
		$newStatement = $this->getStatementProcessorInstance($objectTypeName);
		$newStatement->setReverse();
		
		if ($object !== null) $newStatement->setObject($object);
		$newStatement->setParameters($parameters, true);
		
		// create the database object 
		$newStatement->save();
		
		return $newStatement;
	}
	
	/**
	 * returns all object types for JCoins statements.
	 */
	public function getObjectTypes() {
		return $this->objectTypes; 
	}
	
	/**
	 * Returns the object type for a specific statement.
	 */
	public function getObjectTypeByName($name) {
		if (!isset($this->objectTypes[$name])) {
			throw new SystemException("unknown statement object type name: ". $name);
		}
		
		return $this->objectTypes[$name]; 
	}
	
	/**
	 * Returns a statement processor instance. 
	 */
	public function getStatementProcessorInstance($objectTypeName) {
		$objectType = $this->getObjectTypeByName($objectTypeName);
		
		// We clone the processor, because it is an new 
		// instance of the object 
		$newStatement = clone $objectType->getProcessor();
		
		return $newStatement;
	}
}
