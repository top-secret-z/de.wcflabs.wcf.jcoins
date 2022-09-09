<?php
namespace wcf\system\user\notification\object;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\jcoins\statement\JCoinsStatement;
use wcf\data\user\User;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Transfer notification object
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsTransferNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject {
	/**
	 * @inheritdoc
	 */
	protected static $baseClass = JCoinsStatement::class;
	
	/**
	 * @inheritdoc
	 */
	public function getTitle() {
		$user = new User($this->userID);
		if (!$user->userID) return $user->getLanguage()->get('wcf.user.notification.jcoins.transfer.title');
		
		return WCF::getLanguage()->get('wcf.user.notification.jcoins.transfer.title');
	}
	
	/**
	 * @inheritdoc
	 */
	public function getURL() {
		return LinkHandler::getInstance()->getLink('JCoinsStatementList');
	}
	
	/**
	 * @inheritdoc
	 */
	public function getAuthorID() {
		return $this->author;
	}
}
