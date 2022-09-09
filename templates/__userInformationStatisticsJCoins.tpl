{if MODULE_JCOINS && $__wcf->session->getPermission('user.jcoins.canSee') && JCOINS_SHOW_PROFILE}
	{if $__wcf->session->getPermission('mod.jcoins.canSeeTransferList')}
		<dt><a href="{link controller='GlobalJCoinsStatementList' username=$user->username}{/link}" title="{lang}wcf.jcoins.userJCoins{/lang}" class="jsTooltip">{lang}wcf.jcoins.title{/lang}</a></dt>
	{else}
		<dt>{lang}wcf.jcoins.title{/lang}</dt>
	{/if}
	<dd>{#$user->jCoinsAmount}</dd>
{/if}
