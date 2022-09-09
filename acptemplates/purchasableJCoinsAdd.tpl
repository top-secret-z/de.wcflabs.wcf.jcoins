{include file='header' pageTitle='wcf.acp.purchasableJCoins.'|concat:$action}

{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.purchasableJCoins.{$action}{/lang}</h1>
	</div>

	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='PurchasableJCoinsList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.purchasableJCoins.list{/lang}</span></a></li>

			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<form method="post" action="{if $action == 'add'}{link controller='PurchasableJCoinsAdd'}{/link}{else}{link controller='PurchasableJCoinsEdit' id=$purchasableJCoinsID}{/link}{/if}">
	<div class="section">
		<dl{if $errorField == 'title'} class="formError"{/if}>
			<dt><label for="title">{lang}wcf.global.title{/lang}</label></dt>
			<dd>
				<input type="text" id="title" name="title" value="{$i18nPlainValues['title']}" autofocus="autofocus" class="medium" />
				{if $errorField == 'title'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{elseif $errorType == 'multilingual'}
							{lang}wcf.global.form.error.multilingual{/lang}
						{else}
							{lang}wcf.acp.purchasableJCoins.title.error.{@$errorType}{/lang}
						{/if}
					</small>
				{/if}
			</dd>
		</dl>

		<dl{if $errorField == 'description'} class="formError"{/if}>
			<dt><label for="description">{lang}wcf.global.description{/lang}</label></dt>
			<dd>
				<textarea id="description" name="description" cols="40" rows="10">{$i18nPlainValues[description]}</textarea>
				{if $errorField == 'description'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else}
							{lang}wcf.acp.purchasableJCoins.description.error.{@$errorType}{/lang}
						{/if}
					</small>
				{/if}
			</dd>
		</dl>

		<dl>
			<dt></dt>
			<dd>
				<label><input type="checkbox" name="useHTML" value="1" {if $useHTML}checked="checked" {/if}/> {lang}wcf.acp.purchasableJCoins.useHTML{/lang}</label>
				<small>{lang}wcf.acp.purchasableJCoins.useHTML.description{/lang}</small>
			</dd>
		</dl>

		<dl>
			<dt><label for="showOrder">{lang}wcf.acp.purchasableJCoins.showOrder{/lang}</label></dt>
			<dd>
				<input type="number" id="showOrder" name="showOrder" value="{if $showOrder}{@$showOrder}{/if}" class="tiny" min="0" />
				<small>{lang}wcf.acp.purchasableJCoins.showOrder.description{/lang}</small>
			</dd>
		</dl>

		<dl>
			<dt></dt>
			<dd>
				<label><input type="checkbox" name="isDisabled" value="1" {if $isDisabled}checked="checked" {/if}/> {lang}wcf.acp.purchasableJCoins.isDisabled{/lang}</label>
				<small>{lang}wcf.acp.purchasableJCoins.isDisabled.description{/lang}</small>
			</dd>
		</dl>

		<dl>
			<dt><label for="aviableUntil">{lang}wcf.acp.purchasableJCoins.availableUntil{/lang}</label></dt>
			<dd>
				<label><input type="datetime" id="availableUntil" name="availableUntil" value="{$availableUntil}" placeholder="{lang}wcf.acp.purchasableJCoins.availableUntil{/lang}" /></label>
				<small>{lang}wcf.acp.purchasableJCoins.availableUntil.description{/lang}</small>
			</dd>
		</dl>

		{event name='dataFields'}
	</div>

	<div class="section">
		<h2 class="sectionTitle">{lang}wcf.acp.purchasableJCoins.paymentOptions{/lang}</h2>
		
		<dl{if $errorField == 'cost'} class="formError"{/if}>
			<dt><label for="cost">{lang}wcf.acp.purchasableJCoins.cost{/lang}</label></dt>
			<dd>
				<input type="number" id="cost" name="cost" value="{$cost}" class="tiny" step="0.01" min="0" />
				<select name="currency" id="currency">
					{htmlOptions values=$availableCurrencies output=$availableCurrencies selected=$currency}
				</select>
				{if $errorField == 'cost'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else}
							{lang}wcf.acp.purchasableJCoins.cost.error.{@$errorType}{/lang}
						{/if}
					</small>
				{/if}
			</dd>
		</dl>

		<dl>
			<dt><label for="jCoins">{lang}wcf.acp.purchasableJCoins.jCoins{/lang}</label></dt>
			<dd>
				<input type="number" id="jCoins" name="jCoins" value="{if $jCoins}{$jCoins}{/if}" class="tiny" min="0" />
			</dd>
		</dl>

		{event name='paymentOptionsField'}
	</div>

	{event name='sections'}

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		{csrfToken}
	</div>
</form>

{include file='footer'}