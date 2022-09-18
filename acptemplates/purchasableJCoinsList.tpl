{include file='header' pageTitle='wcf.acp.purchasableJCoins.list'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.purchasableJCoins.list{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='PurchasableJCoinsAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.purchasableJCoins.add{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{hascontent}
    <div class="paginationTop">
        {content}{pages print=true assign=pagesLinks controller='PurchasableJCoinsList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}{/content}
    </div>
{/hascontent}

{if $objects|count}
    <div class="section sortableListContainer">
        <table class="table">
            <thead>
            <tr>
                <th class="columnID columnPurchasableJCoinsID{if $sortField == 'purchasableJCoinsID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='PurchasableJCoinsList'}pageNo={@$pageNo}&sortField=purchasableJCoinsID&sortOrder={if $sortField == 'purchasableJCoinsID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                <th class="columnTitle{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='PurchasableJCoinsList'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.title{/lang}</a></th>
                <th class="columnDigits columnCost{if $sortField == 'cost'} active {@$sortOrder}{/if}"><a href="{link controller='PurchasableJCoinsList'}pageNo={@$pageNo}&sortField=cost&sortOrder={if $sortField == 'cost' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.purchasableJCoins.cost{/lang}</a></th>
                <th class="columnDigits columnJCoins{if $sortField == 'jCoins'} active {@$sortOrder}{/if}"><a href="{link controller='PurchasableJCoinsList'}pageNo={@$pageNo}&sortField=jCoins&sortOrder={if $sortField == 'jCoins' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.purchasableJCoins.jCoins{/lang}</a></th>
                <th class="columnDigits columnShowOrder{if $sortField == 'showOrder'} active {@$sortOrder}{/if}"><a href="{link controller='PurchasableJCoinsList'}pageNo={@$pageNo}&sortField=showOrder&sortOrder={if $sortField == 'showOrder' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.purchasableJCoins.showOrder{/lang}</a></th>

                {event name='columnHeads'}
            </tr>
            </thead>

            <tbody>
            {foreach from=$objects item=purchasableJCoins}
                <tr class="jsPurchasableJCoinsRow">
                    <td class="columnIcon">
                        <span class="icon icon16 fa{if !$purchasableJCoins->isDisabled}-check{/if}-square-o jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if !$purchasableJCoins->isDisabled}disable{else}enable{/if}{/lang}" data-object-id="{@$purchasableJCoins->purchasableJCoinsID}"></span>
                        <a href="{link controller='PurchasableJCoinsEdit' id=$purchasableJCoins->purchasableJCoinsID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
                        <span class="icon icon16 fa-times jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$purchasableJCoins->purchasableJCoinsID}" data-confirm-message="{lang}wcf.acp.purchasableJCoins.delete.confirmMessage{/lang}"></span>

                        {event name='itemButtons'}
                    </td>
                    <td class="columnID columnPurchasableJCoinsID">{@$purchasableJCoins->purchasableJCoinsID}</td>
                    <td class="columnTitle"><a href="{link controller='PurchasableJCoinsEdit' id=$purchasableJCoins->purchasableJCoinsID}{/link}" title="{lang}wcf.global.button.edit{/lang}">{$purchasableJCoins->title|language}</a></td>
                    <td class="columnDigits columnCost">{@$purchasableJCoins->currency} {$purchasableJCoins->cost|currency}</td>
                    <td class="columnDigits columnJCoins">{#$purchasableJCoins->jCoins}</td>
                    <td class="columnDigits columnShowOrder">{$purchasableJCoins->showOrder}</td>

                    {event name='columns'}
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>

    <footer class="contentFooter">
        {hascontent}
            <div class="paginationBottom">
                {content}{@$pagesLinks}{/content}
            </div>
        {/hascontent}

        <nav class="contentFooterNavigation">
            <ul>
                <li><a href="{link controller='PurchasableJCoinsAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.purchasableJCoins.add{/lang}</span></a></li>

                {event name='contentFooterNavigation'}
            </ul>
        </nav>
    </footer>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<script data-relocate="true">
    //<![CDATA[
    $(function() {
        new WCF.Action.Delete('wcf\\data\\purchasable\\jcoins\\PurchasableJCoinsAction', '.jsPurchasableJCoinsRow');
        new WCF.Action.Toggle('wcf\\data\\purchasable\\jcoins\\PurchasableJCoinsAction', '.jsPurchasableJCoinsRow');
    });
    //]]>
</script>

{include file='footer'}
