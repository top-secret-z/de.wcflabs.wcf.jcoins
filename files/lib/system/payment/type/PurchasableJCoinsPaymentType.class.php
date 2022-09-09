<?php
namespace wcf\system\payment\type;
use wcf\data\purchasable\jcoins\transaction\log\PurchasableJCoinsTransactionLogAction;
use wcf\data\purchasable\jcoins\PurchasableJCoins;
use wcf\data\user\User;
use wcf\system\exception\SystemException;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Purchasable JCoins payment type.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsPaymentType extends AbstractPaymentType {
	/**
	 * @inheritdoc
	 */
	public function processTransaction($paymentMethodObjectTypeID, $token, $amount, $currency, $transactionID, $status, $transactionDetails) {
		$user = $purchasableJCoins = null;
		try {
			$tokenParts = explode(':', $token);
			if (count($tokenParts) != 2) {
				throw new SystemException('invalid token');
			}
			list($userID, $purchasableJCoinsID) = $tokenParts;
			
			// get user object
			$user = new User(intval($userID));
			if (!$user->userID) {
				throw new SystemException('invalid user');
			}
			
			// get subscription object
			$purchasableJCoins = new PurchasableJCoins(intval($purchasableJCoinsID));
			if (!$purchasableJCoins->getObjectID()) {
				throw new SystemException('invalid purchasableJCoins' . var_dump($token));
			}
			
			// check for 'duplicate' transactionID
			$sql = "SELECT	COUNT(*) AS count
					FROM	wcf".WCF_N."_purchasable_jcoins_transaction_log
					WHERE	paymentMethodObjectTypeID = ? AND transactionID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$paymentMethodObjectTypeID, $transactionID]);
			if ($statement->fetchColumn()) {
				throw new SystemException('transaction already processed');
			}
			
			$logMessage = '';
			if ($status == 'completed') {
				// validate payment amout
				if ($amount != $purchasableJCoins->cost || $currency != $purchasableJCoins->currency) {
					throw new SystemException('invalid payment amount');
				}
				
				UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.purchasableJCoins', null, [
						'amount' => $purchasableJCoins->jCoins,
						'userID' => $user->userID,
						'title' => WCF::getLanguage()->get($purchasableJCoins->title)
				]);
				
				$logMessage = 'payment completed';
			}
			if ($status == 'reversed') {
				UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.purchasableJCoins', null, [
						'amount' => $purchasableJCoins->jCoins * -1,
						'userID' => $user->userID,
						'title' => WCF::getLanguage()->get($purchasableJCoins->title)
				]);
				
				$logMessage = 'payment reversed';
			}
			if ($status == 'canceled_reversal') {
				UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.purchasableJCoins', null, [
						'amount' => $purchasableJCoins->jCoins,
						'userID' => $user->userID,
						'title' => WCF::getLanguage()->get($purchasableJCoins->title)
				]);
				$logMessage = 'reversal canceled';
			}
			
			$action = new PurchasableJCoinsTransactionLogAction([], 'create', ['data' => [
					'userID' => $user->userID,
					'purchasableJCoinsID' => $purchasableJCoins->purchasableJCoinsID,
					'paymentMethodObjectTypeID' => $paymentMethodObjectTypeID,
					'logTime' => TIME_NOW,
					'transactionID' => $transactionID,
					'logMessage' => $logMessage,
					'transactionDetails' => serialize($transactionDetails)
			]]);
			$action->executeAction();
		} 
		catch (SystemException $e) {
			$action = new PurchasableJCoinsTransactionLogAction([], 'create', ['data' => [
					'userID' => ($user !== null ? $user->userID : null),
					'purchasableJCoinsID' => ($purchasableJCoins !== null) ? $purchasableJCoins->purchasableJCoinsID : null,
					'paymentMethodObjectTypeID' => $paymentMethodObjectTypeID,
					'logTime' => TIME_NOW,
					'transactionID' => $transactionID,
					'logMessage' => $e->getMessage(),
					'transactionDetails' => serialize($transactionDetails)
			]]);
			$action->executeAction();
			
			throw $e;
		}
	}
}
