/**
 * Dialog to pay a paid subscription with JCoins.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
define(['Ajax', 'Language', 'Ui/Dialog', 'Ui/Notification'], function(Ajax, Language, UiDialog, UiNotification) {
	"use strict";
	
	function UZJCoinsPayment(realCost, jCoinsAmount, currency, cost, formattedCost, itemName, uID, token, returnURL) { this.init(realCost, jCoinsAmount, currency, cost, formattedCost, itemName, uID, token, returnURL); }
	
	UZJCoinsPayment.prototype = {
		init: function(template, jCoinsAmount, realCost, currency, cost, formattedCost, itemName, uID, token, returnURL) {
			this.template = template;
			this.jCoinsAmount = jCoinsAmount;
			this.realCost = realCost;
			this.currency = currency;
			this.cost = cost;
			this.formattedCost = formattedCost;
			this.itemName = itemName;
			this.uID = uID;
			this.token = token;
			this.returnURL = returnURL;
			this.dialog = null;
			
			var callback = this._click.bind(this);
			elBySelAll('#jCoinsPurchaseButton-'+ this.uID, null, function(button) {
				button.addEventListener(WCF_CLICK_EVENT, callback)
			})
		},
		
		/**
		 * Initializes the payment dialog.
		 */
		_click() {
			if (this.dialog === null) {
				this.dialog = elCreate('div')
				elAttr(this.dialog, 'id', 'jcoinsOverlay' + this.uID)
				
				this.dialog.innerHTML = this.template.fetch({
					jCoinsAmount:	this.jCoinsAmount,
					realCost:		this.realCost,
					currency:		this.currency,
					cost:			this.formattedCost,
					itemName:		this.itemName,
					token:			this.token,
				})
				
				this.dialog.querySelector('button[data-type="submit"]').addEventListener(WCF_CLICK_EVENT, this._buy.bind(this))
				this.dialog.querySelector('button[data-type="cancel"]').addEventListener(WCF_CLICK_EVENT, this._cancel.bind(this))
				document.body.appendChild(this.dialog);
				
				UiDialog.open(this);
				
				// hide iaw available JCoins
				if (parseInt(this.jCoinsAmount) < parseInt(this.realCost)) {
					elById('jcoinsOk').style.display = 'none';
					elById('jcoinsSubmit').style.display = 'none';
				}
				else {
					elById('jcoinsLow').style.display = 'none';
				}
			} 
			else {
				UiDialog.open(this)
			}
		},
		
		/**
		 * Cancels the payment.
		 */
		_cancel: function() {
			UiDialog.close(this)
		},
		
		/**
		 * Submits the payment.
		 */
		_buy: function() {
			Ajax.api(this, {
				actionName:	'makePayment',
				parameters: {
					token: 		this.token,
					cost: 		this.cost,
					currency: 	this.currency,
					realCost: 	this.realCost,
					itemName: 	this.itemName
				}
			});
		},
		
		_ajaxSuccess: function(data) {
			if (data.returnValues.success) {
				UiNotification.show(Language.get('wcf.jcoins.transfer.success'));
				UiDialog.close(this);
				window.location.reload();
			}
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
				id: 'jcoinsOverlay' + this.uID,
				options: {
					title: Language.get('wcf.payment.jcoins.overlay.title', {
						itemName: this.itemName
					})
				}
			};
		}
	};
	
	return UZJCoinsPayment;
});
