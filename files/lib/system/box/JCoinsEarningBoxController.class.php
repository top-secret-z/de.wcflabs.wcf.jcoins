<?php
namespace wcf\system\box;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\WCF;

/**
 * JCoins earning box controller. 
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsEarningBoxController extends AbstractBoxController {
	/**
	 * @inheritDoc
	 */
	protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];
	
	/**
	 * The definition name for statement objects.
	 */
	const DEFINITION_NAME = 'de.wcflabs.jcoins.statement.object';
	
	/**
	 * JCoins object type categories
	 */
	public $objectTypesCategories = [];
	
	/**
	 * @inheritDoc
	 */
	protected function loadContent() {
		if (!MODULE_JCOINS) return; 
		
		$objectTypes = ObjectTypeCache::getInstance()->getObjectTypes(self::DEFINITION_NAME);
		foreach ($objectTypes as $id => $objectType) {
			if ($objectType->editable === "0") {
				unset($objectTypes[$id]);
			} else if ($objectType->amount === 0 || ($objectType->amount === null && $objectType->defaultAmount == 0)) {
				unset($objectTypes[$id]);
			}
		}
		
		foreach ($objectTypes as $objectType) {
			if (!array_key_exists($objectType->category, $this->objectTypesCategories)) {
				$this->objectTypesCategories[$objectType->category] = [];
			}
			
			$this->objectTypesCategories[$objectType->category][] = $objectType;
		}
		
		if (!count($this->objectTypesCategories)) return '';
		
		WCF::getTPL()->assign([
				'objectTypesCategories' => $this->objectTypesCategories
		]);
		
		$this->content = WCF::getTPL()->fetch('boxJCoinsEarningOverview');
	}
}
