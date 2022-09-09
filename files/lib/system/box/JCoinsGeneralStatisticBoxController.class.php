<?php
namespace wcf\system\box;
use wcf\system\cache\builder\JCoinsStatisticCacheBuilder;
use wcf\system\WCF;

/**
 * JCoins statistic box controller.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsGeneralStatisticBoxController extends AbstractBoxController {
	/**
	 * @inheritDoc
	 */
	protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];
	
	/**
	 * @inheritDoc
	 */
	protected function loadContent() {
		if (!MODULE_JCOINS) return;
		
		WCF::getTPL()->assign([
				'dataArray' => JCoinsStatisticCacheBuilder::getInstance()->getData()
		]);
		
		$this->content = WCF::getTPL()->fetch('boxJCoinsGeneralStatistic');
	}
}
