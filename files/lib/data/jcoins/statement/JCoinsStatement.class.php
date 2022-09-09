<?php
namespace wcf\data\jcoins\statement;
use wcf\data\DatabaseObject;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\comment\Comment;
use wcf\data\comment\response\CommentResponse;
use wcf\data\conversation\Conversation;
use wcf\data\conversation\message\ConversationMessage;
use wcf\data\trophy\Trophy;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\WCF;

/**
 * Represents a JCoins Statement entry.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsStatement extends DatabaseObject {
	/**
	 * @inheritdoc
	 */
	protected static $databaseTableName = 'jcoins_statement';
	
	/**
	 * @inheritdoc
	 */
	protected static $databaseTableIndexName = 'statementID';
	
	/**
	 * the statement object type
	 */
	protected $objectType = null;
	
	/**
	 * the object which is associated with this statement
	 */
	protected $object = null;
	
	/**
	 * user profile which is associated with this statement
	 */
	protected $userProfile = null;
	
	/**
	 * @inheritdoc
	 */
	public function __construct($id, array $row = null, DatabaseObject $object = null) {
		parent::__construct($id, $row, $object);
		
		$this->loadObject();
	}
	
	/**
	 * Load the object for this statement.
	 */
	protected function loadObject() {
		if ($this->objectID != null) {
			$this->objectType = ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID);
			if ($this->objectType->objectClassName !== null && $this->objectType->objectClassName != '*') {
				$className = $this->objectType->objectClassName;
			}
			else if ($this->objectClassName !== null) {
				$className = $this->objectClassName;
			}
			
			if (isset($className) && class_exists($className)) {
				if (is_subclass_of($className, '\wcf\data\DatabaseObjectDecorator')) {
					$baseClassName = call_user_func([$className, 'getBaseClass']);
					
					$baseClass = new $baseClassName($this->objectID);
					$this->object = new $className($baseClass);
				}
				else {
					$this->object = new $className($this->objectID);
				}
			}
			
			// fix for conversation and comments / replies and trophy
			if ($this->object instanceof Conversation && !$this->object->conversationID) $this->object = null;
			if ($this->object instanceof ConversationMessage && !$this->object->messageID) $this->object = null;
			if ($this->object instanceof Comment && !$this->object->commentID) $this->object = null;
			if ($this->object instanceof CommentResponse && !$this->object->responseID) $this->object = null;
			if ($this->object instanceof Trophy && !$this->object->trophyID) $this->object = null;
		}
	}
	
	/**
	 * Returns the object which is associated with this statement.
	 */
	public function getObject() {
		return $this->object;
	}
	
	/**
	 * @inheritdoc
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		$this->data['additionalData'] = @unserialize($this->data['additionalData']);
		if (!is_array($this->data['additionalData'])) {
			$this->data['additionalData'] = [];
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function __get($name) {
		$value = parent::__get($name);
		
		// treat additional data as data variables if it is an array
		if ($value === null && isset($this->data['additionalData'][$name])) {
			$value = $this->data['additionalData'][$name];
		}
		return $value;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getLink() {
		// we can not check, whether the interface ILinkableObject
		// is referenced, because it's not always referenced and 
		// object decorators can have this method with the magic 
		// method __call, so we must check this with is_callable
		if (is_callable([$this->getObject(), 'getLink'])) {
			// try to skip link to objectID 0
			if (($this->getObject() instanceof DatabaseObject)) {
				if (!$this->getObject()->getObjectID()) return '';
			}
			
			return $this->getObject()->getLink();
		}
		
		return '';
	}
	
	/**
	 * Returns the user profile for this statement.
	 */
	public function getUserProfile() {
		if ($this->userProfile === null) {
			$this->userProfile = UserProfileRuntimeCache::getInstance()->getObject($this->userID);
		}
		
		return $this->userProfile;
	}
	
	/**
	 * Sets the user profile for this object.
	 */
	public function setUserProfile(UserProfile $userProfile) {
		$this->userProfile = $userProfile;
	}
	
	/**
	 * Returns the object type which is for the statement.
	 */
	public function getObjectType() {
		if ($this->objectType === null) {
			$this->objectType = ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID);
		}
		
		return $this->objectType;
	}
	
	/**
	 * Returns the reason for the statement.
	 */
	public function getReason() {
		// special case reactions due to change in WS
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
	
	/**
	 * @inheritdoc
	 */
	public function __toString() {
		return $this->getReason();
	}
	
	/**
	 * Returns a transfer reason as message optimized for use in emails.
	 */
	public function getMailText($mimeType = 'text/plain') {
		switch ($mimeType) {
			case 'text/plain':
			case 'text/html':
				if (empty($this->__get('reason'))) return '-';
				return $this->__get('reason');
		}
		
		throw new \LogicException('Unreachable');
	}
}
