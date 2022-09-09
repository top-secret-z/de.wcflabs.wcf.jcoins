<?php
namespace wcf\system\box;
use wcf\system\WCF;

/**
 * JCoins personal history box controller.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsPersonalHistoryPlotSidebarBoxController extends AbstractBoxController {
	/**
	 * @inheritDoc
	 */
	protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];
	
	/**
	 * @inheritDoc
	 */
	protected function loadContent() {
		if (!MODULE_JCOINS ||!WCF::getUser()->userID) return;
		
		$dateTime = \DateTime::createFromFormat('Y-m-d h:i', date('Y-m-d') . " 00:00");
		for ($i = 0; $i < 30; ++$i) {
			$dataArray[$dateTime->getTimestamp()] = 0;
			$dateTime->modify('-1 day');
		}
		
		$stmt = WCF::getDB()->prepareStatement("SELECT FROM_UNIXTIME(time, '%Y-%m-%d') AS day, SUM(amount) as amount FROM wcf". WCF_N ."_jcoins_statement WHERE userID = ? AND time > ? GROUP BY day ORDER BY day DESC LIMIT 30");
		$stmt->execute([
				WCF::getUser()->userID,
				$dateTime->getTimestamp()
		]);
		
		while ($row = $stmt->fetchArray()) {
			$dataArray[strtotime($row['day'])] = $row['amount'];
		}
		
		WCF::getTPL()->assign([
			'dataArray' => $dataArray,
			'minValue' => min(min($dataArray), 0)
		]);
		
		$this->content = WCF::getTPL()->fetch('boxJCoinsHistoryPlot');
	}
}
