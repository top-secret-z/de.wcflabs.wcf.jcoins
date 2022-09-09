<?php
namespace wcf\system\jcoins\statement;
use wcf\data\jcoins\statement\JCoinsStatementAction;
use wcf\data\user\UserAction;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\exception\UserInputException;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;

/**
 * The transfer JCoins statement.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class TransferJCoinsStatement extends DefaultJCoinsStatement {
	/**
	 * Object type for transfers
	 */
	const OBJECT_TYPE = 'de.wcflabs.jcoins.statement.transfer';
	
	/**
	 * @inheritdoc
	 */
	public function validateParameters() {
		parent::validateParameters(); 
		
		if (empty($this->parameters['amount'])) {
			throw new UserInputException('amount');
		}
		
		if (empty($this->parameters['author'])) {
			throw new UserInputException('author');
		}
		
		$userObject = UserProfileRuntimeCache::getInstance()->getObject($this->parameters['author']); 
		
		if (!$userObject->getObjectID()) {
			throw new UserInputException('author');
		}
		
		$this->parameters['username'] = $userObject->getUsername();
	}
	
	/**
	 * @inheritdoc
	 */
	public function calculateAmount() {
		if (isset($this->parameters['amount'])) {
			return $this->parameters['amount']; 
		}
		
		// no amount given
		return 0; 
	}
	
	/**
	 * @inheritdoc
	 */
	protected function saveDatabase() {
		$parameters = $this->getParameters(); 
		
		if (isset($parameters['time'])) {
			$time = $parameters['time'];
			unset($parameters['time']);
		} else {
			$time = false;
		}
		
		unset($parameters['amount']);
		unset($parameters['userID']);
		
		$data = [
				'objectTypeID' => $this->getObjectType()->getObjectID(),
				'amount' => $this->calculateAmount(),
				'additionalData' => serialize($parameters),
				'userID' => $this->parameters['userID'],
				'time' => $time ?: TIME_NOW
		];
		
		if ($this->parameters['userID'] && $this->calculateAmount()) {
			$action = new JCoinsStatementAction([], 'create', [
					'data' => $data
			]);
			$returnValues = $action->executeAction();
			$this->returnValuesLastObject = $returnValues['returnValues'];
			
			// update coins for the user 
			$userAction = new UserAction([$this->parameters['userID']], 'update', [
					'counters' => [
							'jCoinsAmount' => $this->calculateAmount()
					]
			]);
			$userAction->executeAction();
			
			if (!(isset($parameters['moderative']) && $parameters['moderative']) && $this->calculateAmount() > 0) {
				$data = [
						'amount' => $this->calculateAmount() * -1,
						'userID' => $this->parameters['author'],
						'author' => $this->parameters['userID']
				];
				
				if (isset($this->parameters['reason'])) {
					$data['reason'] = $this->parameters['reason']; 
				}
				
				UserJCoinsStatementHandler::getInstance()->create(self::OBJECT_TYPE, null, $data);
			}
		}
	}
}
