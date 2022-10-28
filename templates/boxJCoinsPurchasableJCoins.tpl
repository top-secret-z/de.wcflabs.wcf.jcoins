<section class="section sectionContainerList" id="purchasableJCoinsBox">
    <header class="boxHeadline boxSubHeadline">
        <h2>{lang}wcf.dashboard.box.de.wcflabs.jcoins.sidebar.purchasableJCoins{/lang}</h2>
    </header>

    <ul class="containerBoxList tripleColumned">
        {foreach from=$purchasableJCoins item=item}
            <li>
                <div class="containerHeadline" title="{$item->getDescription(true)}">
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
</section>
