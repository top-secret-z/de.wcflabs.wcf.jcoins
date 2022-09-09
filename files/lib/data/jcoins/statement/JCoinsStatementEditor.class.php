<?php
namespace wcf\data\jcoins\statement;
use wcf\data\DatabaseObjectEditor;

/**
 * Object Editor for JCoins Statement entries.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
class JCoinsStatementEditor extends DatabaseObjectEditor {
	/**
	 * @inheritdoc
	 */
	protected static $baseClass = JCoinsStatement::class;
}
