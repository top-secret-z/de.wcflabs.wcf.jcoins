<?php
namespace wcf\system\box;
use wcf\data\jcoins\statement\JCoinsStatementList;
use wcf\system\box\AbstractDatabaseObjectListBoxController;
use wcf\system\WCF;

/**
 * Dynamic box controller implementation for a list of statements.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsStatementListBoxController extends AbstractDatabaseObjectListBoxController {
	/**
	 * @inheritDoc
	 */
	public $defaultLimit = 5;
	public $maximumLimit = 50;
	
	/**
	 * @inheritDoc
	 */
	protected static $supportedPositions = [
			'sidebarLeft',
			'sidebarRight'
	];
	
	/**
	 * @inheritDoc
	 */
	protected function getObjectList() {
		$statementList = new JCoinsStatementList();
		
		return $statementList;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getTemplate() {
		$templateName = 'boxJCoinsStatementList';
		
		return WCF::getTPL()->fetch($templateName, 'wcf', [
				'boxJCoinsList' => $this->objectList
		], true);
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasContent() {
		if (!MODULE_JCOINS) return false;
		if (!WCF::getSession()->getPermission('user.jcoins.canSee')) return false;
		
		$this->sortField = 'time';
		$this->sortOrder = 'DESC';
		
		return parent::hasContent();
	}
}
