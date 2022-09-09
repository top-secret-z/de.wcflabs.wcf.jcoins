<?php
namespace wcf\system\event\listener;
use wcf\data\jcoins\statement\JCoinsStatementAction;
use wcf\data\jcoins\statement\JCoinsStatementList;

/**
 * Cleans up old jcoins statements.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsCleanUpCronjobListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS) return; 
		
		if (!JCOINS_STATEMENTS_DELETEAFTER) return;
		
		$statementList = new JCoinsStatementList(); 
		$statementList->getConditionBuilder()->add("jcoins_statement.time < ?", [TIME_NOW - 86400 * JCOINS_STATEMENTS_DELETEAFTER]);
		if (JCOINS_STATEMENTS_DELETEONLYTRASHED) $statementList->getConditionBuilder()->add('jcoins_statement.isTrashed = ?', [1]);
		$statementList->readObjects();
		
		if (count($statementList)) {
			$statementAction = new JCoinsStatementAction($statementList->getObjects(), 'delete'); 
			$statementAction->executeAction(); 
		}
	}
}
