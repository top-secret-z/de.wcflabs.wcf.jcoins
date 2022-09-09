{if MODULE_JCOINS && $__wcf->user->userID && $__wcf->session->getPermission('user.jcoins.canUse')}
	<li id="jcoinsPanel" data-count="{#$__wcf->user->jCoinsAmount}">
		<a class="jsTooltip" href="{link controller='JCoinsStatementList'}{/link}" title="{lang}wcf.jcoins.title{/lang}">
			<span class="icon icon32 fa-money"></span>
			<span>{lang}wcf.jcoins.title{/lang}</span>
			{if $__wcf->user->jcoinsShowBadge === null || $__wcf->user->jcoinsShowBadge}
				{if WCF_VERSION|substr:0:3 >= '5.5'}
					<span class="badge jCoinsBadgeUpdate" data-full-amount="{#$__wcf->user->jCoinsAmount}" data-short-amount="{$__wcf->user->jCoinsAmount|shortUnit}">{$__wcf->user->jCoinsAmount|shortUnit}</span>
				{else}
					<span class="badge badgeUpdate" data-full-amount="{#$__wcf->user->jCoinsAmount}" data-short-amount="{$__wcf->user->jCoinsAmount|shortUnit}">{$__wcf->user->jCoinsAmount|shortUnit}</span>
				{/if}
			{/if}
		</a>
		
		{if !OFFLINE || $__wcf->session->getPermission('admin.general.canViewPageDuringOfflineMode')}
			<script data-relocate="true">
				require(['UZ/JCoins/Panel', 'UZ/JCoins/Transfer', 'Language'], function(UZJCoinsPanel, UZJCoinsTransfer, Language) {
					Language.addObject({
						'wcf.jcoins.transfer.title': '{jslang}wcf.jcoins.transfer.title{/jslang}',
						'wcf.jcoins.transfer.success': '{jslang}wcf.jcoins.transfer.success{/jslang}'
					});
					
					$panel = new UZJCoinsPanel({
						noItems: '{lang}wcf.jcoins.noMoreItems{/lang}',
						showAllLink: '{link controller='JCoinsStatementList' encode=false}{/link}',
						title: '{capture assign=jCoinsTitle}{lang}wcf.jcoins.title{/lang}{/capture}{@$jCoinsTitle|encodeJS} {if $__wcf->user->jcoinsShowBadge !== null && !$__wcf->user->jcoinsShowBadge}({#$__wcf->user->jCoinsAmount}){/if}',
					});
					{if $__wcf->session->getPermission('mod.jcoins.canSeeTransferList')}
						$panel._createNewLink('panelGlobalStatementsLink', '{link controller='GlobalJCoinsStatementList' encode=false}{/link}', '{lang}wcf.jcoins.globalStatements{/lang}', 'fa-globe');
					{/if}
					{if MODULE_PURCHASABLE_JCOINS}
						$panel._createNewLink('panelPurchasableJCoinsLink', '{link controller='PurchasableJCoins' encode=false}{/link}', '{capture assign=purchasableJCoinsTitle}{lang}wcf.jcoins.purchasableJCoins{/lang}{/capture}{@$purchasableJCoinsTitle|encodeJS}', 'fa-cart-plus');
					{/if}
					{if $__wcf->session->getPermission('user.jcoins.canTransfer')}
						$panel._createNewLink('panelJCoinsTransferButton', '#', '{lang}wcf.jcoins.transfer.title{/lang}', 'fa-exchange', function () {
							new UZJCoinsTransfer('panelJCoinsTransferButton', '')
						});
					{/if}
					
					{event name='afterInit'}
				});
			</script>
		{/if}
	</li>
{/if}
