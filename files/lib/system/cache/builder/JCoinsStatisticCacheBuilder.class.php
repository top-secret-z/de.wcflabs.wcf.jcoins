<?php
namespace wcf\system\cache\builder;
use wcf\system\WCF;

/**
 * Builds statistics for the current WCF.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsStatisticCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritdoc
	 */
	protected $maxLifetime = 900;
	
	/**
	 * @inheritdoc
	 */
	public function rebuild(array $parameters) {
		$statistic = []; 
		
		// get user stats
		$userCache = UserStatsCacheBuilder::getInstance()->getData();
		
		$statistic['overallJCoins'] = $this->fetchOverallJCoins(); 
		$statistic['JCoinsPerUser'] = round($statistic['overallJCoins'] / $userCache['members'], 2); 
		
		return $statistic; 
	}
	
	/**
	 * Returns the overall JCoins. 
	 * 
	 * @return integer
	 * @throws \wcf\system\database\DatabaseException
	 */
	public function fetchOverallJCoins() {
		$stmt = WCF::getDB()->prepareStatement("SELECT SUM(jCoinsAmount) FROM wcf". WCF_N ."_user");
		$stmt->execute(); 
		
		return $stmt->fetchColumn(); 
	}
}
