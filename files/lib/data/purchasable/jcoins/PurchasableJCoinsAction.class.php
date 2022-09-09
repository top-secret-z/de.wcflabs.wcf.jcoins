<?php
namespace wcf\data\purchasable\jcoins;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;

/**
 * DatabaseObject-related functions for JCoins statements.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class PurchasableJCoinsAction extends AbstractDatabaseObjectAction implements IToggleAction {
	/**
	 * @inheritdoc
	 */
	protected $permissionsDelete = ['admin.purchasableJCoins.canManage'];
	
	/**
	 * @inheritdoc
	 */
	protected $permissionsUpdate = ['admin.purchasableJCoins.canManage'];
	
	/**
	 * @inheritdoc
	 */
	protected $requireACP = ['create', 'delete', 'toggle', 'update'];
	
	/**
	 * @inheritdoc
	 */
	public function validateToggle() {
		parent::validateUpdate();
	}
	
	/**
	 * @inheritdoc
	 */
	public function toggle() {
		foreach ($this->objects as $object) {
			$object->update([
			    'isDisabled' => $object->isDisabled ? 0 : 1
			]);
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function create() {
		$showOrder = 0;
		if (isset($this->parameters['data']['showOrder'])) {
			$showOrder = $this->parameters['data']['showOrder'];
			unset($this->parameters['data']['showOrder']);
		}
		
		$object = parent::create();
		$editor = new PurchasableJCoinsEditor($object);
		$editor->setShowOrder($showOrder);
		
		return new PurchasableJCoins($object->purchasableJCoinsID);
	}
	
	/**
	 * @inheritdoc
	 */
	public function update() {
		parent::update();

		if (count($this->objects) == 1 && isset($this->parameters['data']['showOrder']) && $this->parameters['data']['showOrder'] != reset($this->objects)->showOrder) {
			reset($this->objects)->setShowOrder($this->parameters['data']['showOrder']);
		}
	}
}
