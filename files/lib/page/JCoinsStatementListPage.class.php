<?php
namespace wcf\page;
use wcf\data\jcoins\statement\JCoinsStatementList;
use wcf\system\WCF;

/**
 * Statement list for the user.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsStatementListPage extends SortablePage {
	/**
	 * @inheritdoc
	 */
	public $enableTracking = true;
	
	/**
	 * @inheritdoc
	 */
	public $loginRequired = true;
	
	/**
	 * @inheritdoc
	 */
	public $neededModules = ['MODULE_JCOINS'];
	
	/**
	 * @inheritdoc
	 */
	public $itemsPerPage = JCOINS_STATEMENTS_PER_PAGE;
	
	/**
	 * @inheritdoc
	 */
	public $defaultSortField = 'statementID';
	
	/**
	 * @inheritdoc
	 */
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @inheritdoc
	 */
	public $validSortFields = ['statementID', 'amount', 'time'];
	
	/**
	 * @inheritdoc
	 */
	public $objectListClassName = JCoinsStatementList::class;
	
	/**
	 * @inheritdoc
	 */
	public $neededPermissions = ['user.jcoins.canUse'];
	
	/**
	 * Filter
	 */
	public $objectType = 0;
	public $availableObjectTypes = [];
	
	/**
	 * @inheritdoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (!empty($_REQUEST['objectType'])) $this->objectType = intval($_REQUEST['objectType']);
	}
	
	/**
	 * @inheritdoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		// object types and filter
		if (JCOINS_ALLOW_FILTER) {
			$this->availableObjectTypes = $this->objectList->getAvailableObjectTypes();
			
			if (!empty($this->objectType)) {
				$this->objectList->getConditionBuilder()->add('jcoins_statement.objectTypeID = ?', [$this->objectType]);
			}
		}
		
		$this->objectList->getConditionBuilder()->add("jcoins_statement.userID = ? AND jcoins_statement.isTrashed = 0", [WCF::getUser()->userID]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables () {
		parent::assignVariables();
		
		if (!JCOINS_ALLOW_FILTER) {
			$this->availableObjectTypes = [];
			$this->objectType = 0;
		}
		
		WCF::getTPL()->assign([
				'objectType' => $this->objectType,
				'availableObjectTypes' => $this->availableObjectTypes
		]);
	}
}
