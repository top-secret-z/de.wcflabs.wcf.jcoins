{capture assign='pageTitle'}{lang}wcf.acp.purchasableJCoins.transactionLog{/lang}: {@$log->logID}{/capture}
{include file='header'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.purchasableJCoins.transactionLog{/lang}: {@$log->logID}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='PurchasableJCoinsTransactionLogList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.purchasableJCoins.transactionLog.list{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

<section class="section">
    <h2 class="sectionTitle">{lang}wcf.acp.purchasableJCoins.transactionLog{/lang}: {@$log->logID}</h2>

    <dl>
        <dt>{lang}wcf.acp.purchasableJCoins.transactionLog.logMessage{/lang}</dt>
        <dd>{$log->logMessage}</dd>

        {if $log->userID}
            <dt>{lang}wcf.user.username{/lang}</dt>
            <dd><a href="{link controller='UserEdit' id=$log->userID}{/link}" title="{lang}wcf.acp.user.edit{/lang}">{$log->getUser()->username}</a></dd>
        {/if}

        {if $log->purchasableJCoinsID}
            <dt>{lang}wcf.global.title{/lang}</dt>
            <dd>{$log->getSubscription()->title|language}</dd>
        {/if}

        <dt>{lang}wcf.acp.purchasableJCoins.transactionLog.paymentMethod{/lang}</dt>
        <dd>{lang}wcf.payment.{@$log->getPaymentMethodName()}{/lang}</dd>

        <dt>{lang}wcf.acp.purchasableJCoins.transactionLog.transactionID{/lang}</dt>
        <dd>{$log->transactionID}</dd>

        <dt>{lang}wcf.acp.purchasableJCoins.transactionLog.logTime{/lang}</dt>
        <dd>{@$log->logTime|time}</dd>
    </dl>
</section>

<section class="section">
    <h2 class="sectionTitle">{lang}wcf.acp.purchasableJCoins.transactionLog.transactionDetails{/lang}</h2>

    <dl>
        {foreach from=$log->getTransactionDetails() key=key item=value}
            <dt>{$key}</dt>
            <dd>{$value}</dd>
        {/foreach}
    </dl>
</section>

{event name='sections'}

<div class="contentNavigation">
    <nav>
        <ul>
            <li><a href="{link controller='PurchasableJCoinsTransactionLogList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.purchasableJCoins.transactionLog.list{/lang}</span></a></li>

            {event name='contentNavigationButtonsBottom'}
        </ul>
    </nav>
</div>

{include file='footer'}
