<?php
namespace wcf\page;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Purchasable JCoins return page.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsReturnPage extends AbstractPage {
	/**
	 * @inheritdoc
	 */
	public $templateName = 'redirect';
	
	/**
	 * @inheritdoc
	 */
	public $neededModules = ['MODULE_PURCHASABLE_JCOINS', 'MODULE_JCOINS'];
	
	/**
	 * @inheritdoc
	 */
	public $neededPermissions = ['user.jcoins.canUse'];
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'message' => WCF::getLanguage()->getDynamicVariable('wcf.jcoins.purchasableJCoins.returnMessage'),
				'wait' => 30,
				'url' => LinkHandler::getInstance()->getLink()
		]);
	}
}
