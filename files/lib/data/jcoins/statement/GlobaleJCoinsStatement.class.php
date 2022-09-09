<?php
namespace wcf\data\jcoins\statement;
use wcf\data\conversation\Conversation;
use wcf\data\conversation\message\ConversationMessage;
use wcf\system\WCF;

/**
 * Represents a JCoins Statement entry for global statement list.
 * 
 * Extension of JCoinsStatement doesn't work fully => simple copy of class
 * + basis for future adjustments
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class GlobaleJCoinsStatement extends JCoinsStatement {
	/**
	 * @inheritdoc
	 */
	public function getLink() {
		// modify link for conversations
		if ($this->getObject() instanceof ConversationMessage || $this->getObject() instanceof Conversation) {
			return '';
		}
		
		return parent::getLink();
	}
	
	/**
	 * Returns the reason for the statement.
	 */
	public function getReason() {
		// conversation
		if ($this->getObject() instanceof ConversationMessage || $this->getObject() instanceof Conversation) {
			return WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.reasonGlobal.' . (($this->reverseMode) ? 'reverse.' : '') . $this->getObjectType()->objectType, [
					'statement' => $this,
					'object' => $this->getObject()
			]);
		}
		
		// transfer
		if ($this->getObjectType()->objectType == 'de.wcflabs.jcoins.statement.transfer') {
			return WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.reasonGlobal.' . (($this->reverseMode) ? 'reverse.' : '') . $this->getObjectType()->objectType, [
					'statement' => $this,
					'object' => $this->getObject()
			]);
		}
		
		// reactions
		if (isset($this->additionalData['objectClassName'])) {
			if ($this->objectType->objectType == 'de.wcflabs.jcoins.statement.reaction') {
				try {
					$object = null;
					$className = $this->additionalData['objectClassName'];
					
					if (isset($className) && class_exists($className)) {
						if (is_subclass_of($className, '\wcf\data\DatabaseObjectDecorator')) {
							$baseClassName = call_user_func([$className, 'getBaseClass']);
							$object = new $baseClassName($this->objectID);
						}
						else {
							$object = new $className($this->objectID);
						}
					}
				}
				catch (\Exception $e) {
					$object = null;
				}
				
				if (!$object || !$object->getObjectID()) {
					return WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.reason.' . (($this->reverseMode) ? 'reverse.' : '') . $this->getObjectType()->objectType . '.noObject', [
							'statement' => $this,
							'object' => null
					]);
				}
			}
		}
		
		return WCF::getLanguage()->getDynamicVariable('wcf.jcoins.statement.reason.' . (($this->reverseMode) ? 'reverse.' : '') . $this->getObjectType()->objectType, [
				'statement' => $this,
				'object' => $this->getObject()
		]);
	}
}
