{capture assign='headContent'}
	{if PURCHASABLE_JCOINS_ENABLE_TOS_CONFIRMATION}
		<script data-relocate="true">
			$(function() {
				$('#tosConfirmed').change(function () {
					if ($('#tosConfirmed').is(':checked')) {
						$('.paidSubscriptionList button').enable();
					}
					else {
						$('.paidSubscriptionList button').disable();
					}
				});
				$('#tosConfirmed').change();
			});
		</script>
		
		<noscript>
			<style type="text/css">
				.paidSubscriptionList button {
					display: none;
				}
			</style>
		</noscript>
	{/if}
{/capture}

{include file='header'}

{if $purchasableJCoins|count}
	<section class="section sectionContainerList paidSubscriptionList">
		{if PURCHASABLE_JCOINS_ENABLE_TOS_CONFIRMATION}
			<div class="sectionDescription">
				<label><input type="checkbox" id="tosConfirmed" name="tosConfirmed" value="1"> {lang}wcf.jcoins.confirmTOS{/lang}</label>
			</div>
		{/if}
		
		<ul class="containerList">
			{foreach from=$purchasableJCoins item=item}
				<li>
					<div class="containerHeadline">
						<h3>{$item->title|language}</h3>
						<p>{@$item->getDescription()}</p>

						<p class="marginTopTiny">{lang}wcf.jcoins.purchasableJCoins.formattedCost{/lang}</p>

						<ul class="buttonList marginTopTiny">
							{foreach from=$item->getPurchaseButtons() item=button}
								<li>{@$button}</li>
							{/foreach}
						</ul>
					</div>
				</li>
			{/foreach}
		</ul>
	</section>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{include file="__jCoinsBranding"}

{include file='footer'}