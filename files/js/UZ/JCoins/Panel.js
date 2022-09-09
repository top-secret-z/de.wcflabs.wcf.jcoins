/**
 * Dropdown panel for JCoins.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		de.wcflabs.wcf.jcoins
 */
define(['StringUtil', 'EventHandler', 'Dom/ChangeListener'], function (StringUtil, EventHandler, DomChangeListener) {
	"use strict";
	
	// there is no new API
	var UZJCoinsPanel = WCF.User.Panel.Abstract.extend({
		init: function (options) {
			options.enableMarkAsRead = false
			
			this._super($('#jcoinsPanel'), 'jcoinsPanel', options)
			
			EventHandler.add('com.woltlab.wcf.UserMenuMobile', 'more', (function (data) {
				if (data.identifier == 'de.wcflabs.wcf.jcoins.UserMenuMobilePanel') {
					this.toggle();
				}
			}).bind(this))
		},
		
		_initDropdown: function () {
			var $dropdown = this._super();
			
			EventHandler.fire('de.wcflabs.wcf.jcoins.Panel', 'initedDropdown', {
				dropdown: $dropdown,
				class: this
			});
			
			DomChangeListener.trigger();
			
			return $dropdown
		},
		
		_createNewLink: function (id, link, title, icon, success) {
			EventHandler.add('de.wcflabs.wcf.jcoins.Panel', 'initedDropdown', function (data) {
				var dropdown = data.dropdown
				console.log(dropdown)
				var button = elCreate('li')
				button.id = id
				var span = elCreate('a')
				span.href = link
				span.title = title
				elData(span, 'tooltip', title)
				span.classList.add('jsTooltip')
				
				var iconSpan = elCreate('span')
				iconSpan.classList.add('icon', 'icon16', icon, 'pointer')
				span.appendChild(iconSpan)
				button.appendChild(span)
				
				dropdown.getLinkList().append(button)
				
				if (typeof success === 'function') {
					success()
				}
			})
		},
		
		_closeDropdown: function () {
			this._dropdown.close()
		},
		
		_load: function () {
			this._proxy.setOption('data', {
				actionName: 'getStatementList',
				className: 'wcf\\data\\jcoins\\statement\\JCoinsStatementAction'
			})
			this._proxy.sendRequest()
		},
		
		updateBadge: function (count) {
			if (this._badge === null) {
				return;
			}
			
			this._badge.text(StringUtil.shortUnit(count));
			elData(elBySel("#jcoinsPanel .badge"), 'short-amount', StringUtil.shortUnit(count))
			elData(elBySel("#jcoinsPanel .badge"), 'full-amount', StringUtil.formatNumeric(count))
		}
	});
	
	return UZJCoinsPanel
});
