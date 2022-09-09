<?php
namespace wcf\system\payment\method;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\OptionUtil;
use wcf\util\StringUtil;

/**
 * The JCoins payment method.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsPaymentMethod extends AbstractPaymentMethod {
	/**
	 * @inheritdoc
	 */
	public function getSupportedCurrencies() {
		$option = OptionUtil::parseSelectOptions(JCOINS_CURRENCY_TO_COINS);
		
		return array_keys($option);
	}
	
	/**
	 * @inheritdoc
	 */
	public function getPurchaseButton($cost, $currency, $name, $token, $returnURL, $cancelReturnURL, $isRecurring = false, $subscriptionLength = 0, $subscriptionLengthUnit = '') {
		$jCoinsCurrency = OptionUtil::parseSelectOptions(JCOINS_CURRENCY_TO_COINS); 
		
		if (!isset($jCoinsCurrency[$currency])) {
			throw new SystemException("Unknown currency for JCoins");
		}
		
		$conversion = floatval($jCoinsCurrency[$currency]);
		$realCost = round($conversion * $cost); 
		$template = WCF::getTPL()->fetch('jcoinsPurchaseOverlay');
		$buttonUID = substr(StringUtil::getRandomID(), 0, 5);
		$jCoinsAmount = WCF::getUser()->jCoinsAmount;
		
		WCF::getTPL()->assign([
				'cost' => $cost,
				'name' => $name,
				'token' => $token,
				'returnURL' => $returnURL,
				'currency' => $currency,
				'realCost' => $realCost,
				'template' => $template,
				'buttonUID' => $buttonUID,
				'jCoinsAmount' => $jCoinsAmount
		]);
		
		return WCF::getTPL()->fetch('jcoinsPurchaseButton');
	}
}
