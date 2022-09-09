<?php
namespace wcf\system\user\notification\object\type;
use wcf\data\jcoins\statement\JCoinsStatement;
use wcf\data\jcoins\statement\JCoinsStatementList;
use wcf\system\user\notification\object\JCoinsTransferNotificationObject;

/**
 * The user JCoins statement handler, which should be used to create statements.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsTransferNotificationObjectType extends AbstractUserNotificationObjectType {
	/**
	 * @inheritdoc
	 */
	protected static $decoratorClassName = JCoinsTransferNotificationObject::class;
	
	/**
	 * @inheritdoc
	 */
	protected static $objectClassName = JCoinsStatement::class;
	
	/**
	 * @inheritdoc
	 */
	protected static $objectListClassName = JCoinsStatementList::class;
	
}
