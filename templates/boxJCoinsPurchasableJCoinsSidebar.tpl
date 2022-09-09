<ul class="sidebarBoxList">
	{foreach from=$purchasableJCoins item=item}
		<li>
			<div class="sidebarBoxHeadline" title="{$item->getDescription(true)}">
				<h3>{$item->title|language}</h3>
				<small>{lang}wcf.jcoins.purchasableJCoins.formattedCost{/lang}</small>
			</div>

			<ul class="buttonList marginTopTiny">
				{foreach from=$item->getPurchaseButtons() item=button}
					<li>{@$button}</li>
				{/foreach}
			</ul>
		</li>
	{/foreach}
</ul>
