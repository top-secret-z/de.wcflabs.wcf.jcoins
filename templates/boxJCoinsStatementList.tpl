<ul class="sidebarItemList">
	{foreach from=$boxJCoinsList item=jCoins}
		{assign var='profile' value=$jCoins->getUserProfile()}
		<li class="box24">
			<a href="{link controller='User' object=$profile}{/link}" aria-hidden="true">{@$profile->getAvatar()->getImageTag(24)}</a>
			
			<div class="sidebarItemTitle">
				<h3>{user object=$profile}</h3>
				<small>{#$jCoins->amount} {lang}{JCOINS_NAME}{/lang} <span class="separatorLeft">{@$jCoins->time|time}</span></small>
			</div>
		</li>
	{/foreach}
</ul>
