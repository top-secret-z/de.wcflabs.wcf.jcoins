<?php
namespace wcf\page;
use wcf\data\jcoins\statement\GlobaleJCoinsStatementList;
use wcf\data\user\User;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Global statement list page.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class GlobalJCoinsStatementListPage extends SortablePage {
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
	public $validSortFields = ['statementID', 'amount', 'time', 'username'];
	
	/**
	 * @inheritdoc
	 */
	public $objectListClassName = GlobaleJCoinsStatementList::class;
	
	/**
	 * @inheritdoc
	 */
	public $neededPermissions = ['mod.jcoins.canSeeTransferList'];
	
	/**
	 * User
	 */
	public $user = null;
	
	/**
	 * Filter
	 */
	public $username = '';
	public $objectType = '';
	public $availableObjectTypes = [];
	
	/**
	 * @inheritdoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (!empty($_REQUEST['username'])) $this->username = StringUtil::trim($_REQUEST['username']);
		if (!empty($_REQUEST['objectType'])) $this->objectType = intval($_REQUEST['objectType']);
	}
	
	/**
	 * @inheritdoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		// object types
		$this->availableObjectTypes = $this->objectList->getAvailableObjectTypes();
		
		// filter
		if (!empty($this->username)) {
			$user = User::getUserByUsername($this->username);
			if ($user->userID) {
				$this->objectList->getConditionBuilder()->add('jcoins_statement.userID = ?', [$user->userID]);
			}
			else {
				$this->objectList->getConditionBuilder()->add('jcoins_statement.userID = ?', [0]);
			}
		}
		
		if (!empty($this->objectType)) {
			$this->objectList->getConditionBuilder()->add('jcoins_statement.objectTypeID = ?', [$this->objectType]);
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables () {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'username' => $this->username,
				'objectType' => $this->objectType,
				'availableObjectTypes' => $this->availableObjectTypes
		]);
	}
}
