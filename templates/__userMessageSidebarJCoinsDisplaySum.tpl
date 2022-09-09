{if MODULE_JCOINS && $__wcf->session->getPermission('user.jcoins.canSee') && MESSAGE_SIDEBAR_JCOINS}
	{if $__wcf->session->getPermission('mod.jcoins.canSeeTransferList')}
		<dt><a href="{link controller='GlobalJCoinsStatementList' username=$userProfile->username}{/link}" title="{lang user=$userProfile}wcf.jcoins.userJCoins{/lang}" class="jsTooltip">{lang}wcf.jcoins.title{/lang}</a></dt>
	{else}
		<dt>{lang}wcf.jcoins.title{/lang}</dt>
	{/if}
	<dd>{#$userProfile->jCoinsAmount}</dd>
{/if}
