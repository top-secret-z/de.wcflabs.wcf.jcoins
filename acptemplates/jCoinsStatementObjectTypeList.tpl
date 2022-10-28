{include file='header' pageTitle='wcf.acp.menu.link.jcoinsStatementObjectType'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.menu.link.jcoinsStatementObjectType{/lang}</h1>
    </div>

    {hascontent}
    <nav class="contentHeaderNavigation">
        <ul>
            {content}{event name='contentHeaderNavigation'}{/content}
        </ul>
    </nav>
    {/hascontent}
</header>

{include file='formError'}

{if $success|isset}
    <p class="success">{lang}wcf.global.success.edit{/lang}</p>
{/if}

<form method="post" action="{link controller='JCoinsStatementObjectTypeList'}{/link}">
    {foreach from=$objectTypeCategories item='category'}
        <section class="section">
            <h2 class="sectionTitle">{lang}wcf.jcoins.statement.category.{$category}{/lang}</h2>
            {foreach from=$objectTypesByCategory[$category] item='objectType'}
                <dl{if $errorField == $objectType->objectTypeID} class="formError"{/if}>
                    <dt><label for="{@$objectType->objectType}">{lang}wcf.jcoins.statement.title.{$objectType->objectType}{/lang}</label></dt>
                    <dd>
                        <input type="number" id="{@$objectType->objectType}" name="amount[{@$objectType->objectTypeID}]" value="{$amount[$objectType->objectTypeID]}" required="required" class="tiny" />
                    </dd>
                </dl>
                {if $objectType->retractable === "1"}
                    <dl{if $errorField == $objectType->objectTypeID} class="formError"{/if}>
                        <dt><label for="{@$objectType->objectType}">{lang}wcf.jcoins.statement.title.retractable.{$objectType->objectType}{/lang}</label></dt>
                        <dd>
                            <input type="number" id="{@$objectType->objectType}" name="retractableAmount[{@$objectType->objectTypeID}]" value="{$retractableAmount[$objectType->objectTypeID]}" required="required" class="tiny" />
                        </dd>
                    </dl>
                {/if}
            {/foreach}
        </section>
    {/foreach}

    <div class="formSubmit">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
        {csrfToken}
    </div>
</form>

{include file='footer'}
