<?php
namespace wcf\data\purchasable\jcoins\transaction\log;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\purchasable\jcoins\PurchasableJCoins;
use wcf\data\user\User;
use wcf\data\DatabaseObject;

/**
 * Represents a purchasable JCoins transaction log entry.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsTransactionLog extends DatabaseObject {
	/**
	 * @inheritdoc
	 */
	protected static $databaseTableName = 'purchasable_jcoins_transaction_log';
	
	/**
	 * @inheritdoc
	 */
	protected static $databaseTableIndexName = 'logID';
	
	/**
	 * user object
	 */
	protected $user = null;
	
	/**
	 * paid subscription object
	 */
	protected $purchasableJCoins = null;
	
	/**
	 * Returns the payment method of this transaction.
	 */
	public function getPaymentMethodName() {
		$objectType = ObjectTypeCache::getInstance()->getObjectType($this->paymentMethodObjectTypeID);
		return $objectType->objectType;
	}
	
	/**
	 * Returns transaction details.
	 */
	public function getTransactionDetails() {
		return unserialize($this->transactionDetails);
	}
	
	/**
	 * Returns the user of this transaction.
	 */
	public function getUser() {
		if ($this->user === null) {
			$this->user = new User($this->userID);
		}
		
		return $this->user;
	}
	
	/**
	 * Returns the purchasable JCoins of this transaction.
	 */
	public function getSubscription() {
		if ($this->purchasableJCoins === null) {
			$this->purchasableJCoins = new PurchasableJCoins($this->purchasableJCoinsID);
		}
		
		return $this->purchasableJCoins;
	}
}
