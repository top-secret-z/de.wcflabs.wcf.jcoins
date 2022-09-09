<?php
namespace wcf\system\condition;
use wcf\data\condition\Condition;
use wcf\data\user\User;
use wcf\data\user\UserList;
use wcf\system\WCF;

/**
 * Condition implementation for JCoins of a user.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class UserJCoinsAmountCondition extends AbstractIntegerCondition implements IContentCondition, IUserCondition {
	/**
	 * @inheritdoc
	 */
	protected $identifier = 'jCoinsAmount';
	
	/**
	 * @inheritdoc
	 */
	protected $label = 'wcf.user.condition.jcoins';
	
	/**
	 * @inheritdoc
	 */
	public function addUserCondition(Condition $condition, UserList $userList) {
		if ($condition->greaterThan !== null) {
			$userList->getConditionBuilder()->add('user_table.jCoinsAmount > ?', [$condition->greaterThan]);
		}
		if ($condition->lessThan !== null) {
			$userList->getConditionBuilder()->add('user_table.jCoinsAmount < ?', [$condition->lessThan]);
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function checkUser(Condition $condition, User $user) {
		if ($condition->greaterThan !== null && $user->jCoinsAmount <= $condition->greaterThan) {
			return false;
		}
		if ($condition->lessThan !== null && $user->jCoinsAmount >= $condition->lessThan) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @inheritdoc
	 */
	public function showContent(Condition $condition) {
		if (!WCF::getUser()->userID) return false;
		
		return $this->checkUser($condition, WCF::getUser());
	}
}
