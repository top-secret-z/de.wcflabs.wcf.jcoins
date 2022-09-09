/**
 * Dialog to transfer JCoins.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
define(['Ajax', 'Language', 'Ui/Dialog', 'Ui/Notification'], function(Ajax, Language, UiDialog, UiNotification) {
	"use strict";
	
	function UZJCoinsTransfer(triggerButton, defaultUser) { this.init(triggerButton, defaultUser); }
	
	UZJCoinsTransfer.prototype = {
		init: function(triggerButton, defaultUser) {
			this._defaultUser = defaultUser;
			this._submitted = false;
			var trigger = triggerButton;
			var button = elById(trigger);
			button.addEventListener(WCF_CLICK_EVENT, this._showDialog.bind(this));
		},
		
		/**
		 * Submits the transfer.
		 */
		_submit: function() {
			if (this._submitted) {
				return;
			}
			this._submitted = true;
			
			var receiver = elById('receiverInput').value;
			var amount = elById('amountInput').value;
			var reason = elById('reasonInput').value;
			var moderative = 0;
			
			var moderativeInput = elById('moderativeInput');
			if (moderativeInput && moderativeInput.checked) { moderative = 1; }
			
			Ajax.api(this, {
				actionName:	'transfer',
				parameters:	{
					receiver:	receiver,
					amount:		amount,
					reason:		reason,
					moderative:	moderative
				}
			});
		},
		
		/**
		 * cancel just closes the dialog
		 */
		_cancel: function() {
			UiDialog.close(this);
		},
		
		/**
		 * Initializes the transfer dialog.
		 */
		_showDialog: function(event) {
			event.preventDefault();
			
			Ajax.api(this, {
				actionName:	'getTransferOverlay',
				parameters:	{ }
			});
		},
		
		_ajaxSuccess: function(data) {
			switch (data.actionName) {
				case 'getTransferOverlay':
					this._render(data);
					break;
				case 'transfer':
					// receiver
					var receiverInput = elById('transferOverlayReceiverDl');
					var innerError = elBySel('.innerError', receiverInput.parentNode);
					if (innerError !== null) elRemove(innerError);
					
					if (data.returnValues.receiverError) {
						innerError = elCreate('small');
						innerError.className = 'innerError';
						innerError.textContent = data.returnValues.receiverError;
						receiverInput.parentNode.insertBefore(innerError, receiverInput.nextElementSibling);
						this._submitted = false;
						return;
					}
					
					// amount
					var amountInput = elById('transferOverlayAmountDl');
					var innerError = elBySel('.innerError', amountInput.parentNode);
					if (innerError !== null) elRemove(innerError);
					
					if (data.returnValues.amountError) {
						innerError = elCreate('small');
						innerError.className = 'innerError';
						innerError.textContent = data.returnValues.amountError;
						amountInput.parentNode.insertBefore(innerError, amountInput.nextElementSibling);
						this._submitted = false;
						return;
					}
					
					UiNotification.show(Language.get('wcf.jcoins.transfer.success'));
					UiDialog.close(this);
					break;
			}
		},
		
		/**
		 * Opens the transfer dialog.
		 */
		_render: function(data) {
			UiDialog.open(this, data.returnValues.template);
			
			var input = elById('receiverInput');
			input.value = this._defaultUser;
			
			var submitButton = elBySel('.jsTransferSubmit');
			submitButton.addEventListener(WCF_CLICK_EVENT, this._submit.bind(this));
			
			var cancelButton = elBySel('.jsTransferCancel');
			cancelButton.addEventListener(WCF_CLICK_EVENT, this._cancel.bind(this));
			
			this._submitted = false;
		},
		
		_ajaxSetup: function() {
			return {
				data: {
					className: 'wcf\\data\\jcoins\\statement\\JCoinsStatementAction',
				}
			};
		},
		
		_dialogSetup: function() {
			return {
				id: 		'jcoinsTransferOverlay',
				options: 	{ title: Language.get('wcf.jcoins.transfer.title') },
				source: 	null
			};
		}
	};
	
	return UZJCoinsTransfer;
});
