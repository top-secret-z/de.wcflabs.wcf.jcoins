<?php
namespace wcf\system\cache\builder;

/**
 * Caches a list of the members with the most JCoins.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsRichestMembersCacheBuilder extends AbstractSortedUserCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected $sortField = 'jCoinsAmount';
}
