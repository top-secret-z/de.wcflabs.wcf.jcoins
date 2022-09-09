<?php
namespace wcf\system\user\notification\event;
use wcf\system\email\Email;
use wcf\system\request\LinkHandler;

/**
 * The user JCoins statement handler, which should be used to create statements.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsTransferNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @inheritdoc
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.user.notification.jcoins.transfer.title');
	}
	
	/**
	 * @inheritdoc
	 */
	public function getMessage() {
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.jcoins.transfer.message', [
				'statement' => $this->userNotificationObject,
				'author' => $this->author
		]);
	}
	
	/**
	 * @inheritdoc
	 */
/*	public function getEmailMessage($notificationType = 'instant') {
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.jcoins.transfer.mail', [
				'statement' => $this->userNotificationObject,
				'author' => $this->author
		]);
	}
*/
	/**
	 * @inheritDoc
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$messageID = '<de.wcflabs.jcoins.statement/'.$this->getUserNotificationObject()->statementID.'@'.Email::getHost().'>';
		
		return [
				'template' => 'email_notification_jCoinsTransfer',
				'application' => 'wcf',
				'in-reply-to' => [$messageID],
				'references' => [
						$messageID
				],
				'variables' => [
						'languageVariablePrefix' => 'wcf.user.notification.jcoins.transfer'
				]
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('JCoinsStatementList');
	}
}
