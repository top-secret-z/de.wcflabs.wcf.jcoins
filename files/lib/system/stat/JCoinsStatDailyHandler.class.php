<?php
namespace wcf\system\stat;
use wcf\system\stat\AbstractStatDailyHandler;

/**
 * Stat handler implementation for number of JCoins statements.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsStatDailyHandler extends AbstractStatDailyHandler {
	/**
	 * @inheritDoc
	 */
	public function getData($date) {
		return [
			'counter' => $this->getCounter($date, 'wcf'.WCF_N.'_jcoins_statement', 'time'),
			'total' => $this->getTotal($date, 'wcf'.WCF_N.'_jcoins_statement', 'time')
		];
	}
}
