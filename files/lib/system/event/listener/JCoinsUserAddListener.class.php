<?php
namespace wcf\system\event\listener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;

/**
 * Add JCoins for new user.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsUserAddListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// module required
		if (!MODULE_JCOINS) return;
		
		if ($eventObj->getActionName() != 'create') return;
		
		$return = $eventObj->getReturnValues();
		$user = $return['returnValues']; 
		
		UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.register', $user);
	}
}
