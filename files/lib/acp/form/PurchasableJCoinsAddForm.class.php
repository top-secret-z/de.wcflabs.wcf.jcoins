<?php
namespace wcf\acp\form;
use wcf\data\purchasable\jcoins\PurchasableJCoinsAction;
use wcf\data\purchasable\jcoins\PurchasableJCoinsEditor;
use wcf\form\AbstractForm;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\payment\method\PaymentMethodHandler;
use wcf\system\WCF;

/**
 * Provides the purchasable jcoins add form.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsAddForm extends AbstractForm {
	/**
	 * @inheritdoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.purchasableJCoins.add';
	
	/**
	 * @inheritdoc
	 */
	public $neededModules = ['MODULE_JCOINS', 'MODULE_PURCHASABLE_JCOINS'];
	
	/**
	 * @inheritdoc
	 */
	public $neededPermissions = ['admin.purchasableJCoins.canManage'];
	
	/**
	 * purchasableJCoins title
	 */
	public $title = '';
	
	/**
	 * purchasableJCoins description
	 */
	public $description = '';
	
	/**
	 * indicates if the purchasableJCoins is disabled
	 */
	public $isDisabled = 0;
	
	/**
	 * purchasableJCoins show order
	 */
	public $showOrder = 0;
	
	/**
	 * purchasableJCoins cost
	 */
	public $cost = 0.0;
	
	/**
	 * purchasableJCoins currency
	 */
	public $currency = 'EUR';
	
	/**
	 * indicates the jcoins amount
	 */
	public $jCoins = 0;
	
	/**
	 * indicates if the purchasableJCoins use HTML in description
	 */
	public $useHTML = 0;
	
	/**
	 * available until
	 */
	public $availableUntil = '';
	
	/**
	 * list of available currencies
	 */
	public $availableCurrencies = [];
	
	/**
	 * @inheritdoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('title');
		
		if (!count(PaymentMethodHandler::getInstance()->getPaymentMethods())) {
			throw new NamedUserException(WCF::getLanguage()->get('wcf.acp.purchasableJCoins.error.noPaymentMethods'));
		}
		
		// get available currencies
		foreach (PaymentMethodHandler::getInstance()->getPaymentMethods() as $paymentMethod) {
			$this->availableCurrencies = array_merge($this->availableCurrencies, $paymentMethod->getSupportedCurrencies());
		}
		$this->availableCurrencies = array_unique($this->availableCurrencies);
		sort($this->availableCurrencies);
	}
	
	/**
	 * @inheritdoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// read i18n values
		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = I18nHandler::getInstance()->getValue('description');
		if (I18nHandler::getInstance()->isPlainValue('title')) $this->title = I18nHandler::getInstance()->getValue('title');
		
		if (!empty($_POST['isDisabled'])) $this->isDisabled = 1;
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
		if (isset($_POST['cost'])) $this->cost = floatval($_POST['cost']);
		if (isset($_POST['currency'])) $this->currency = $_POST['currency'];
		if (isset($_POST['jCoins'])) $this->jCoins = $_POST['jCoins'];
		if (isset($_POST['useHTML'])) $this->useHTML = $_POST['useHTML'];
		if (isset($_POST['availableUntil'])) $this->availableUntil = $_POST['availableUntil'];
	}
	
	/**
	 * @inheritdoc
	 */
	public function validate() {
		parent::validate();
		
		if (!I18nHandler::getInstance()->validateValue('title')) {
			if (I18nHandler::getInstance()->isPlainValue('title')) {
				throw new UserInputException('title');
			} else {
				throw new UserInputException('title', 'multilingual');
			}
		}
		
		if (!I18nHandler::getInstance()->validateValue('description', false, true)) {
			throw new UserInputException('description');
		}
		
		if ($this->cost <= 0) {
			throw new UserInputException('cost');
		}
		
		if (!in_array($this->currency, $this->availableCurrencies)) {
			throw new UserInputException('cost');
		}
		
		if ($this->jCoins <= 0) {
			throw new UserInputException('jCoins');
		}
		
		if (!empty($this->availableUntil) && @strtotime($this->availableUntil) === false) {
			throw new UserInputException('availableUntil');
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function save() {
		parent::save();
		
		// save subscription
		$this->objectAction = new PurchasableJCoinsAction([], 'create', ['data' => array_merge($this->additionalFields, [
			'title' => $this->title,
			'description' => $this->description,
			'isDisabled' => $this->isDisabled,
			'showOrder' => $this->showOrder,
			'cost' => $this->cost,
			'currency' => $this->currency,
			'jCoins' => $this->jCoins,
			'useHTML' => $this->useHTML, 
			'availableUntil' => (!empty($this->availableUntil)) ? strtotime($this->availableUntil) : 0
		])]);
		$returnValues = $this->objectAction->executeAction();
		
		// save i18n values
		$updateValues = [];
		
		if (!I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->save('description', 'wcf.acp.purchasableJCoins.offer' . $returnValues['returnValues']->purchasableJCoinsID . '.description', 'wcf.acp.purchasableJCoins');
			
			$updateValues['description'] = 'wcf.acp.purchasableJCoins.offer' . $returnValues['returnValues']->purchasableJCoinsID . '.description';
		}
		
		if (!I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->save('title', 'wcf.acp.purchasableJCoins.offer' . $returnValues['returnValues']->purchasableJCoinsID . '.title', 'wcf.acp.purchasableJCoins');
			
			$updateValues['title'] = 'wcf.acp.purchasableJCoins.offer' . $returnValues['returnValues']->purchasableJCoinsID . '.title';
		}
		
		if (!empty($updateValues)) {
			// update database
			$editor = new PurchasableJCoinsEditor($returnValues['returnValues']);
			$editor->update($updateValues);
		}
		
		$this->saved();
		
		// reset values
		$this->title = $this->description = $this->availableUntil = '';
		$this->useHTML = $this->isDisabled = $this->showOrder = $this->cost = $this->jCoins = 0;
		$this->currency = 'EUR';
		I18nHandler::getInstance()->reset();
		
		// show success
		WCF::getTPL()->assign([
			'success' => true
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		
		WCF::getTPL()->assign([
			'action' => 'add',
			'isDisabled' => $this->isDisabled,
			'showOrder' => $this->showOrder,
			'cost' => $this->cost,
			'currency' => $this->currency,
			'jCoins' => $this->jCoins,
			'availableCurrencies' => $this->availableCurrencies,
			'useHTML' => $this->useHTML, 
			'availableUntil' => $this->availableUntil
		]);
	}
}
