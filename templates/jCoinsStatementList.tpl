{capture assign='pageTitle'}{$__wcf->getActivePage()->getTitle()}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}

{capture assign='contentTitle'}{$__wcf->getActivePage()->getTitle()} <span class="badge">{#$items}</span>{/capture}

{capture assign='contentDescription'}{lang}wcf.jcoins.statement.balance{/lang}{/capture}

{capture assign='headContent'}
    {if $pageNo < $pages}
        <link rel="next" href="{link controller='JCoinsStatementList'}pageNo={@$pageNo+1}{/link}">
    {/if}
    {if $pageNo > 1}
        <link rel="prev" href="{link controller='JCoinsStatementList'}{if $pageNo > 2}pageNo={@$pageNo-1}{/if}{/link}">
    {/if}
{/capture}

{assign var='linkParameters' value=''}
{if $objectType}{capture append=linkParameters}&objectType={@$objectType|rawurlencode}{/capture}{/if}

{if WCF_VERSION|substr:0:3 >= '5.5'}
    {capture assign='contentHeaderNavigation'}
        {if $objects|count > 0}
            <li><button class="trashAll"><span class="icon icon16 fa-trash-o"></span> <span>{lang}wcf.jcoins.statement.trash{/lang}</span></button></li>
        {/if}
    {/capture}

    {capture assign='contentInteractionPagination'}
        {pages print=true assign='pagesLinks' controller='JCoinsStatementList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
    {/capture}

    {include file='header'}
{else}
    {capture assign='contentHeaderNavigation'}
        {if $objects|count > 0}
            <li><button class="trashAll"><span class="icon icon16 fa-trash-o"></span> <span>{lang}wcf.jcoins.statement.trash{/lang}</span></button></li>
        {/if}
    {/capture}

    {include file='header'}

    {hascontent}
        <div class="paginationTop">
            {content}
                {pages print=true assign='pagesLinks' controller='JCoinsStatementList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
            {/content}
        </div>
    {/hascontent}
{/if}

{if JCOINS_ALLOW_FILTER}
    {if $objects|count > 0}
        <form method="post" action="{link controller='JCoinsStatementList'}{/link}">
            <section class="section">
                <div class="row rowColGap formGrid">
                    <dl class="col-xs-12 col-md-4">
                        <dt></dt>
                        <dd>
                            <select name="objectType" id="objectType">
                                <option value="">{lang}wcf.jcoins.earnings{/lang}</option>
                                {htmlOptions options=$availableObjectTypes selected=$objectType}
                            </select>
                        </dd>
                    </dl>

                    <dl class="formSubmit col-xs-12 col-md-4">
                        <dt></dt>
                        <dd>
                            <input type="submit" value="{lang}wcf.global.filter{/lang}" accesskey="s">
                            {csrfToken}
                        </dd>
                    </dl>
                </div>
            </section>
        </form>
    {/if}
{/if}

{if $objects|count}
    <div class="section tabularBox messageGroupList jCoinsStatementList">
        <table class="table">
            <thead>
            <tr>
                <th class="columnID{if $sortField == 'statementID'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsStatementList'}pageNo={@$pageNo}&sortField=statementID&sortOrder={if $sortField == 'statementID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                <th class="columnText">{lang}wcf.jcoins.statement.reason{/lang}</th>
                <th class="columnAmount columnDigits{if $sortField == 'amount'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsStatementList'}pageNo={@$pageNo}&sortField=amount&sortOrder={if $sortField == 'amount' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.jcoins.statement.amount{/lang}</a></th>
                <th class="columnDate{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsStatementList'}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.jcoins.statement.date{/lang}</a></th>
            </tr>
            </thead>

            <tbody>
            {foreach from=$objects item=item}
                <tr class="statementTableRow">
                    <td class="columnID">{#$item->statementID}</td>
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
                {content}

                    {event name='contentFooterNavigation'}
                {/content}
            </ul>
        </nav>
    {/hascontent}
</footer>

{include file="__jCoinsBranding"}

<script data-relocate="true">
    require(['Language'], function(Language) {
        Language.addObject({
            'wcf.jcoins.statement.trash.confirmMessage': '{lang}wcf.jcoins.statement.trash.confirmMessage{/lang}'
        });
    });

    require(['UZ/JCoins/Trash'], function(UZJCoinsTrash) {
        new UZJCoinsTrash();
    });
</script>

{include file='footer'}
