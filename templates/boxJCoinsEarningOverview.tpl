<ol class="boxMenu forceOpen sidebarJCoinsEarningOverviewList">
    {foreach from=$objectTypesCategories item=entries key=category}
        <li>
            <span class="boxMenuLink">
                <span class="jCoinsObjectTypeCategory boxMenuLinkTitle">{lang}wcf.jcoins.statement.category.{$category}{/lang}</span>
            </span>
            <ol class="boxMenuDepth1">
                {foreach from=$entries item=entry}
                    {if $entry->amount}
                        {assign var=amount value=$entry->amount}
                    {elseif $entry->defaultAmount}
                        {assign var=amount value=$entry->defaultAmount}
                    {else}
                        {assign var=amount value="0"}
                    {/if}
                    <li>
                        <span class="boxMenuLink">
                            <span class="jCoinsObjectType boxMenuLinkTitle">{lang}wcf.jcoins.statement.title.{$entry->objectType}{/lang}</span>
                            <span class="{if $amount > 0}positive{else}negative{/if}Amount amount">{#$amount}</span>
                        </span>
                    </li>
                {/foreach}
            </ol>
        </li>
    {/foreach}
</ol>
