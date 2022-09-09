<?php
namespace wcf\data\purchasable\jcoins;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\DatabaseObject;
use wcf\system\payment\method\JCoinsPaymentMethod;
use wcf\system\payment\method\PaymentMethodHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a purchasable JCoins entry.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoins extends DatabaseObject {
	/**
	 * @inheritdoc
	 */
	protected static $databaseTableName = 'purchasable_jcoins';
	
	/**
	 * @inheritdoc
	 */
	protected static $databaseTableIndexName = 'purchasableJCoinsID';
	
	/**
	 * Returns the description for this offer. 
	 */
	public function getDescription($stripHTML = false) {
		if ($this->useHTML) {
			if ($stripHTML) {
				return StringUtil::stripHTML(WCF::getLanguage()->get($this->description));
			}
			
			return WCF::getLanguage()->get($this->description);
		}
		
		return nl2br(StringUtil::encodeHTML(WCF::getLanguage()->get($this->description)));
	}
	
	/**
	 * Returns list of purchase buttons.
	 */
	public function getPurchaseButtons() {
		$objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.payment.type', 'de.wcflabs.jcoins.payment.type.purchasableJCoins');
		$buttons = [];
		foreach (PaymentMethodHandler::getInstance()->getPaymentMethods() as $paymentMethod) {
			// check supported currencies
			if (!in_array($this->currency, $paymentMethod->getSupportedCurrencies())) continue;
			
			// jcoins can't buy with jcoins 
			if ($paymentMethod instanceof JCoinsPaymentMethod) continue;
			
			$buttons[] = $paymentMethod->getPurchaseButton($this->cost, $this->currency, WCF::getLanguage()->get($this->title), $objectTypeID . ':' . WCF::getUser()->userID . ':' . $this->purchasableJCoinsID, LinkHandler::getInstance()->getLink('PurchasableJCoinsReturn'), LinkHandler::getInstance()->getLink());
		}
		
		return $buttons;
	}
}
