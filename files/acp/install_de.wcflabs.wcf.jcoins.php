<?php
use wcf\data\option\Option;
use wcf\data\option\OptionEditor;

/**
 * Define install dates.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */

$option = Option::getOptionByName('jcoins_install_date');
$optionEditor = new OptionEditor($option);
$optionEditor->update([
		'optionValue' => TIME_NOW
]);

$option = Option::getOptionByName('jcoins_install_conv_date');
$optionEditor = new OptionEditor($option);
$optionEditor->update([
		'optionValue' => TIME_NOW
]);