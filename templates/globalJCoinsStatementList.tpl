{capture assign='pageTitle'}{$__wcf->getActivePage()->getTitle()}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}

{capture assign='contentTitle'}{$__wcf->getActivePage()->getTitle()} <span class="badge">{#$items}</span>{/capture}

{capture assign='headContent'}
	{if $pageNo < $pages}
		<link rel="next" href="{link controller='GlobalJCoinsStatementList'}pageNo={@$pageNo+1}{/link}">
	{/if}
	{if $pageNo > 1}
		<link rel="prev" href="{link controller='GlobalJCoinsStatementList'}{if $pageNo > 2}pageNo={@$pageNo-1}{/if}{/link}">
	{/if}
{/capture}

{capture assign='sidebarLeft'}
	<section class="box">
		<h2 class="boxTitle">{lang}wcf.global.filter{/lang}</h2>
		
		<form method="post" action="{link controller='GlobalJCoinsStatementList'}{/link}">
			<div class="section">
				<dl>
					<dt></dt>
					<dd>
						<select name="objectType" id="objectType">
							<option value="">{lang}wcf.jcoins.earnings{/lang}</option>
							{htmlOptions options=$availableObjectTypes selected=$objectType}
						</select>
					</dd>
				</dl>
			</div>
			
			<div class="section">
				<dl>
					<dt></dt>
					<dd>
						<input type="text" name="username" class="long" value="{$username}" placeholder="{lang}wcf.user.username{/lang}" autocomplete="off">
					</dd>
				</dl>
			</div>
			<div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
				{csrfToken}
			</div>
		</form>
	</section>
	
	{event name='sidebarBoxes'}
{/capture}

{assign var='linkParameters' value=''}
{if $username}{capture append=linkParameters}&username={@$username|rawurlencode}{/capture}{/if}
{if $objectType}{capture append=linkParameters}&objectType={@$objectType|rawurlencode}{/capture}{/if}

{if WCF_VERSION|substr:0:3 >= '5.5'}
	{capture assign='contentInteractionPagination'}
		{pages print=true assign='pagesLinks' controller='GlobalJCoinsStatementList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
	{/capture}
	
	{include file='header'}
{else}
	{include file='header'}
	
	{hascontent}
		<div class="paginationTop">
			{content}
				{pages print=true assign='pagesLinks' controller='GlobalJCoinsStatementList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
			{/content}
		</div>
	{/hascontent}
{/if}

{if $objects|count}
	<div class="section tabularBox messageGroupList jCoinsStatementList">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID{if $sortField == 'statementID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='GlobalJCoinsStatementList'}pageNo={@$pageNo}&sortField=statementID&sortOrder={if $sortField == 'statementID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnUser{if $sortField == 'username'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='GlobalJCoinsStatementList'}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.user.username{/lang}</a></th>
					<th class="columnText">{lang}wcf.jcoins.statement.reason{/lang}</th>
					<th class="columnAmount columnDigits{if $sortField == 'amount'} active {@$sortOrder}{/if}"><a href="{link controller='GlobalJCoinsStatementList'}pageNo={@$pageNo}&sortField=amount&sortOrder={if $sortField == 'amount' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.jcoins.statement.amount{/lang}</a></th>
					<th class="columnDate{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link controller='GlobalJCoinsStatementList'}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.jcoins.statement.date{/lang}</a></th>
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=item}
					{assign var='userProfile' value=$item->getUserProfile()}
					
					<tr class="statementTableRow">
						<td class="columnIcon">
							{if $item->isTrashed}
								<span class="fa fa-trash jsTooltip" title="{lang}wcf.global.state.trashed{/lang}"></span>
							{/if}
							{if $item->moderative}
								<span class="fa fa-magic jsTooltip" title="{lang}wcf.jcoins.transfer.moderative{/lang}"></span>
							{/if}
							{if $item->amount == 47 || $item->amount == 74}
								<span class="fa fa-hand-spock-o jsTooltip" title="Live long and prosper!"></span>
							{/if}
						</td>
						<td class="columnID">{#$item->statementID}</td>
						<td class="columnIcon columnAvatar">
							{if $userProfile->getAvatar()}
								<div>
									{@$userProfile->getAvatar()->getImageTag(48)}
								</div>
							{/if}
						</td>
						<td class="columnText columnUsername">
							<h3>
								<a href="{link controller='User' object=$userProfile->getDecoratedObject()}{/link}">{$userProfile->username}</a>
							</h3>
						</td>
						<td class="columnReason">{if $item->getLink()}<a href="{$item->getLink()}">{/if}{@$item}{if $item->getLink()}</a>{/if}</td>
						<td class="columnAmount columnDigits"><span class="{if $item->amount > 0}positive{else}negative{/if}Amount">{#$item->amount}</span></td>
						<td class="columnDate">{@$item->time|time}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
	{hascontent}
		<div class="paginationBottom">
			{content}{@$pagesLinks}{/content}
		</div>
	{/hascontent}
	
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{include file="__jCoinsBranding"}

<script data-relocate="true">
	require(['WoltLabSuite/Core/Ui/User/Search/Input'], function(UiUserSearchInput) {
		new UiUserSearchInput(elBySel('input[name="username"]'));
	});
</script>

{include file='footer'}