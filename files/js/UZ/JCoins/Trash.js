/**
 * Clears the user statement list.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
define(['Ajax', 'Language', 'Ui/Confirmation'], function(Ajax, Language, UiConfirmation) {
	"use strict";
	
	function UZJCoinsTrash() { this.init(); }
	
	UZJCoinsTrash.prototype = {
		init: function() {
			var button = elBySel('.trashAll');
			if (button !== null) {
				button.addEventListener(WCF_CLICK_EVENT, this._click.bind(this));
			}
		},
		
		_click: function(event) {
			UiConfirmation.show({
				confirm: function() {
					Ajax.apiOnce({
						data: {
							actionName: 'trashAll',
							className: 'wcf\\data\\jcoins\\statement\\JCoinsStatementAction'
						},
						success: function() {
							window.location.reload();
						}
					});
				},
				message: Language.get('wcf.jcoins.statement.trash.confirmMessage')
			});	
		}
	};
	return UZJCoinsTrash;
});
