<?php
namespace wcf\system\box;
use wcf\data\purchasable\jcoins\PurchasableJCoinsCache;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * JCoins purchasable JCoins box controller.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsBoxController extends AbstractBoxController {
	/**
	 * @inheritDoc
	 */
	protected static $supportedPositions = ['contentBottom', 'contentTop', 'sidebarLeft', 'sidebarRight'];
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('PurchasableJCoins');
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasLink() {
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function loadContent() {
		if (!MODULE_JCOINS) return;
		
		$purchasableJCoins = PurchasableJCoinsCache::getInstance()->getPurchasableJCoins();
		
		if (!count($purchasableJCoins)) return; 
		
		if ($this->getBox()->position == 'contentTop' || $this->getBox()->position == 'contentBottom') {
			$templateName = 'boxJCoinsPurchasableJCoins';
		}
		else {
			$templateName = 'boxJCoinsPurchasableJCoinsSidebar';
		}
		
		WCF::getTPL()->assign([
				'purchasableJCoins' => $purchasableJCoins
		]);
		
		$this->content = WCF::getTPL()->fetch($templateName);
	}
}
