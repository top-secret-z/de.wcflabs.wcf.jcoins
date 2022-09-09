<?php
namespace wcf\system\worker;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\request\LinkHandler;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * Transfer worker for JCoins.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsTransferWorker extends AbstractWorker {
	/**
	 * Transfer data session variable name.
	 */
	const DATA_VARIABLE_NAME = 'JCOINS_TRANSFER_WORKER_DATA';
	
	/**
	 * condition builder object
	 */
	protected $conditions = null;
	
	/**
	 * @inheritdoc
	 */
	protected $limit = 50;
	
	/**
	 * transfer data
	 */
	protected $transferData = null;
	
	/**
	 * @inheritdoc
	 */
	public function validate() {
		WCF::getSession()->checkPermissions(['admin.jcoins.canMassProcess']);
		
		if (!isset($this->parameters['transferID'])) {
			throw new \InvalidArgumentException("transferID missing");
		}
		
		$userTransferData = WCF::getSession()->getVar(self::DATA_VARIABLE_NAME);
		
		if (!isset($userTransferData[$this->parameters['transferID']])) {
			throw new \InvalidArgumentException("transferID '" . $this->parameters['transferID'] . "' is invalid");
		}
		
		$transferData = $userTransferData[$this->parameters['transferID']];
		
		if (isset($transferData['conditions']) && $transferData['conditions'] instanceof PreparedStatementConditionBuilder) {
			$this->conditions = $transferData['conditions'];
		} else if (isset($transferData['userIDs']) && is_array($transferData['userIDs'])) {
			$this->conditions = new PreparedStatementConditionBuilder();
			$this->conditions->add("user_table.userID IN (?)", [$transferData['userIDs']]);
		} else {
			// match all users
			$this->conditions = new PreparedStatementConditionBuilder();
		}
		
		if (!isset($transferData['amount']) || !$transferData['amount']) {
			throw new \InvalidArgumentException("illegal data (amount)");
		}
		
		if (!isset($transferData['reason'])) {
			throw new \InvalidArgumentException("illegal data (reason)");
		}
		
		$this->transferData = $transferData;
	}
	
	/**
	 * @inheritdoc
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
				FROM	wcf" . WCF_N . "_user user_table
				" . $this->conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->conditions->getParameters());
		
		$this->count = $statement->fetchSingleColumn();
	}
	
	/**
	 * @inheritdoc
	 */
	public function getProgress() {
		$progress = parent::getProgress();
		
		if ($progress == 100) {
			// clear session
			$userTransferData = WCF::getSession()->getVar(self::DATA_VARIABLE_NAME);
			unset($userTransferData[$this->parameters['transferID']]);
			WCF::getSession()->register(self::DATA_VARIABLE_NAME, $userTransferData);
		}
		
		return $progress;
	}
	
	/**
	 * @inheritdoc
	 */
	public function execute() {
		// get users
		$sql = "SELECT		user_option.*, user_table.*
				FROM		wcf" . WCF_N . "_user user_table
				LEFT JOIN	wcf" . WCF_N . "_user_option_value user_option
				ON		(user_option.userID = user_table.userID)
				" . $this->conditions . "
				ORDER BY	user_table.userID";
		$statement = WCF::getDB()->prepareStatement($sql, $this->limit, ($this->limit * $this->loopCount));
		$statement->execute($this->conditions->getParameters());
		while ($row = $statement->fetchArray()) {
			UserJCoinsStatementHandler::getInstance()->create('de.wcflabs.jcoins.statement.transfer', null, [
					'amount' => $this->transferData['amount'],
					'author' => WCF::getUser()->userID,
					'userID' => $row['userID'],
					'reason' => $this->transferData['reason'],
					'moderative' => true
			]);
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function getProceedURL() {
		return LinkHandler::getInstance()->getLink('UserList');
	}
}
