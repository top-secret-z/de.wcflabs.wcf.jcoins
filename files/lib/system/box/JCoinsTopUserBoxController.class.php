<?php
namespace wcf\system\box;
use wcf\data\user\UserProfileList;
use wcf\system\box\AbstractBoxController;
use wcf\system\WCF;

/**
 * JCoins top user box controller.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsTopUserBoxController extends AbstractBoxController {
	/**
	 * @inheritDoc
	 */
	protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];
	
	/**
	 * @inheritDoc
	 */
	protected function loadContent() {
		// module and permission
		if (!MODULE_JCOINS) return;
		if (!WCF::getSession()->getPermission('user.jcoins.canSee')) return;
		
		$userProfileList = new UserProfileList();
		$userProfileList->getConditionBuilder()->add('user_table.jcoinsAmount > ?', [0]);
		$userProfileList->sqlOrderBy = 'jcoinsAmount DESC';
		$userProfileList->sqlLimit = 5;
		$userProfileList->readObjects();
		
		if (count($userProfileList)) {
			WCF::getTPL()->assign([
					'topUser' => $userProfileList
			]);
			
			$this->content = WCF::getTPL()->fetch('boxJCoinsTopUser');
		}
	}
}
