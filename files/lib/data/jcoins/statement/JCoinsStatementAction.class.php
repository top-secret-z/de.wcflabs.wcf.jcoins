<?php
namespace wcf\data\jcoins\statement;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\UserProfile;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\jcoins\statement\TransferJCoinsStatement;
use wcf\system\payment\type\IPaymentType;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\user\notification\object\JCoinsTransferNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\OptionUtil;
use wcf\util\StringUtil;

/**
 * DatabaseObject-related functions for JCoins statements.
 * 
 * <strong>Heads up:</strong> Please do not create statements directly with this class! You can use 
 * \wcf\system\user\jcoins\UserJCoinsStatementHandler instead, this is much nicer and follows my own code policies. 
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsStatementAction extends AbstractDatabaseObjectAction {
	/**
	 * Transfers JCoins from a user to an other user.
	 */
	public function transfer() {
		// receiver
		if (empty($this->parameters['receiver'])) return ['receiverError' => WCF::getLanguage()->get('wcf.jcoins.transfer.error.receiver.empty')];
		
		$receivers = ArrayUtil::trim(explode(',', $this->parameters['receiver']));
		$ignoredUser = $invalidUser = [];
		foreach ($receivers as $receiver) {
			$user = UserProfile::getUserProfileByUsername($receiver);
			if (!$user) {
				$invalidUser[] = $receiver;
				continue;
			}
			if ($user->isIgnoredUser(WCF::getUser()->userID)) {
				$ignoredUser[] = $user->username;
				continue;
			}
			$this->parameters['receiverObjects'][$user->userID] = $user;
		}
		
		if (!empty($invalidUser)) {
			$text = WCF::getLanguage()->getDynamicVariable('wcf.jcoins.transfer.error.receiver.invalid', [
					'count' => count($invalidUser),
					'usernames' => implode(', ', $invalidUser)
			]);
			return ['receiverError' => $text];
		}
		if (!empty($ignoredUser)) {
			$text = WCF::getLanguage()->getDynamicVariable('wcf.jcoins.transfer.error.ignoring', [
					'count' => count($ignoredUser),
					'usernames' => implode(', ', $ignoredUser)
			]);
			return ['receiverError' => $text];
		}
		if (empty($this->parameters['receiverObjects'])) {
			$text =  WCF::getLanguage()->get('wcf.jcoins.transfer.error.receiver.empty');
			return ['receiverError' => $text];
		}
		
		// amount + fee
		if (empty($this->parameters['amount'])) {
			$text =  WCF::getLanguage()->get('wcf.jcoins.transfer.error.notNull');
			return ['amountError' => $text];
		}
		if ($this->parameters['amount'] < 0 && !$this->parameters['moderative']) {
			$text =  WCF::getLanguage()->get('wcf.jcoins.transfer.error.positive');
			return ['amountError' => $text];
		}
		
		if (!$this->parameters['moderative']) {
			$toPay = count($this->parameters['receiverObjects']) * $this->parameters['amount'];
			
			if (WCF::getSession()->getPermission('user.jcoins.mustPayTransferFee')) {
				$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.wcflabs.jcoins.statement.object', 'de.wcflabs.jcoins.statement.transfer.fee');
				$toPay -= $objectType->amount * count($this->parameters['receiverObjects']);
			}
			
			if ($toPay > WCF::getUser()->jCoinsAmount) return ['amountError' => WCF::getLanguage()->getDynamicVariable('wcf.jcoins.transfer.error.tooMuch')];
		}
		
		foreach ($this->parameters['receiverObjects'] as $receiver) {
			$statement = UserJCoinsStatementHandler::getInstance()->create(TransferJCoinsStatement::OBJECT_TYPE, null, [
					'amount' => $this->parameters['amount'],
					'reason' => $this->parameters['reason'],
					'author' => WCF::getUser()->userID,
					'userID' => $receiver->userID,
					'moderative' => $this->parameters['moderative'],
					'authorname' => WCF::getUser()->username
			]);
			UserNotificationHandler::getInstance()->fireEvent('jCoinsTransfer', 'de.wcflabs.wcf.jcoins.transfer.notification', new JCoinsTransferNotificationObject($statement->getLastObject()), [$receiver->userID]);
			
			if (!$this->parameters['moderative'] && WCF::getSession()->getPermission('user.jcoins.mustPayTransferFee')) {
				UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.transfer.fee', WCF::getUser());
			}
		}
		
		return ['success' => 1];
	}
	
	/**
	 * Validates the "transfer" action.
	 */
	public function validateTransfer() {
		$this->readBoolean('moderative', true);
		if ($this->parameters['moderative'] && !WCF::getSession()->getPermission('mod.jcoins.canTransferModerative')) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Returns the rendered transfer overlay. 
	 * 
	 * @return array
	 */
	public function getTransferOverlay() {
		// check for transfer fee
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.wcflabs.jcoins.statement.object', 'de.wcflabs.jcoins.statement.transfer.fee');
		
		WCF::getTPL()->assign([
				'transferFee' => $objectType->amount,
				'transferFeeAbs' => abs($objectType->amount)
		]);
		
		return [
				'template' => WCF::getTPL()->fetch('jCoinsTransferOverlay'),
		];
	}
	
	/**
	 * Validates the "getTransferOverlay" action.
	 */
	public function validateGetTransferOverlay() {
		WCF::getSession()->checkPermissions([
				'user.jcoins.canTransfer'
		]);
	}
	
	/**
	 * Returns a user statement list for the user panel. 
	 * 
	 * @return array
	 */
	public function getStatementList() {
		$statementList = new JCoinsStatementList();
		$statementList->getConditionBuilder()->add('jcoins_statement.userID = ?', [WCF::getUser()->userID]);
		$statementList->getConditionBuilder()->add('jcoins_statement.isTrashed = ?', [0]);
		$statementList->sqlLimit = 5;
		$statementList->sqlOrderBy = 'jcoins_statement.time DESC';
		$statementList->readObjects();
		
		$parameters = [
			'statementList' => $statementList
		];
		
		EventHandler::getInstance()->fireAction($this, 'getStatementList', $parameters);
		
		$statementList = $parameters['statementList'];
		
		WCF::getTPL()->assign([
			'statementList' => $statementList
		]);
		
		return [
			'template' => WCF::getTPL()->fetch('statementListUserPanel'),
			'totalCount' => WCF::getUser()->jCoinsAmount
		];
	}
	
	/**
	 * Validates the "getTransferOverlay" action.
	 */
	public function validateGetStatementList() {
		WCF::getSession()->checkPermissions([
			'user.jcoins.canUse'
		]);
	}
	
	/**
	 * Validates the "getTransferOverlay" action.
	 */
	public function validateMakePayment() {
		
		$this->readString('token');
		$this->readInteger('realCost');
		$this->readString('cost');
		$this->readString('currency');
		$this->readString('itemName');
		
		// make cost floatval
		$this->parameters['cost'] = floatval($this->parameters['cost']);
		
		$this->parameters['paymentMethode'] = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.payment.method', 'com.woltlab.wcf.payment.method.jcoins');
		
		if (!in_array($this->parameters['currency'], $this->parameters['paymentMethode']->getProcessor()->getSupportedCurrencies())) {
			throw new UserInputException('currency');
		}
		
		if ($this->parameters['realCost'] > WCF::getUser()->jCoinsAmount) {
			throw new PermissionDeniedException();
		}
		
		// check real costs 
		$jCoinsCurrency = OptionUtil::parseSelectOptions(JCOINS_CURRENCY_TO_COINS);
		
		if (!isset($jCoinsCurrency[$this->parameters['currency']])) {
			throw new SystemException("Unknown currency for JCoins");
		}
		
		$conversion = floatval($jCoinsCurrency[$this->parameters['currency']]);
		
		$realCost = round($conversion * $this->parameters['cost']);
		
		if ($this->parameters['realCost'] != $realCost) {
			throw new IllegalLinkException();
		}
		
		$this->parameters['tokenParts'] = explode(':', $this->parameters['token'], 2);
		
		if (count($this->parameters['tokenParts']) != 2) {
			throw new IllegalLinkException();
		}
		
		$this->parameters['paymentTypeObjectType'] = ObjectTypeCache::getInstance()->getObjectType(intval($this->parameters['tokenParts'][0]));
		
		if ($this->parameters['paymentTypeObjectType'] === null || !($this->parameters['paymentTypeObjectType']->getProcessor() instanceof IPaymentType)) {
			throw new IllegalLinkException();
		}
		
		$this->parameters['paymentTypeObjectTypeProcessor'] = $this->parameters['paymentTypeObjectType']->getProcessor();
	}
	
	/**
	 * Make a payment for a payable object. 
	 * 
	 * @return array
	 */
	public function makePayment() {
		$statement = UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.payment', null, [
			'itemName' => StringUtil::truncate($this->parameters['itemName']),
			'userID' => WCF::getUser()->userID,
			'amount' => $this->parameters['realCost'] * -1
		]);
		
		$object = $statement->getLastObject();
		
		$this->parameters['paymentTypeObjectTypeProcessor']->processTransaction(ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.payment.method', 'com.woltlab.wcf.payment.method.jcoins'), $this->parameters['tokenParts'][1], $this->parameters['cost'], $this->parameters['currency'], $object->getObjectID(), 'completed', []);
		
		return [
			'success' => true
		];
	}
	
	/**
	 * Validates the "trashAll" action.
	 */
	public function validateTrashAll() {
		WCF::getSession()->checkPermissions(['user.jcoins.canUse']);
		
		if (!empty($this->objects) || !empty($this->objectIDs)) {
			throw new IllegalLinkException();
		}
		
		if (!empty($this->parameters)) {
			throw new IllegalLinkException();
		}
		
		$list = new JCoinsStatementList();
		$list->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
		$list->decoratorClassName = 'wcf\data\jcoins\statement\JCoinsStatementEditor';
		$list->readObjects();
		$this->objects = $list->getObjects();
		
		if ($this->objects < 2) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Marks the given statements as trashed.
	 */
	public function trashAll() {
		$this->parameters['data']['isTrashed'] = 1;
		
		parent::update();
	}
}
