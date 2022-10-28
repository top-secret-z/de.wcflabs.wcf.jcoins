{foreach from=$statementList item=statement}
    <li class="statement" data-link="{link controller='JCoinsStatementList' object=$statement}{/link}" data-object-id="{@$statement->statementID}">
        <div class="box32">
            <div>
                <h3><a href="{link controller='JCoinsStatementList' object=$statement}{/link}">{lang}wcf.jcoins.statement.title.{$statement->getObjectType()->objectType}{/lang}</a></h3>
                <small>{@$statement->time|time}</small>
            </div>
            <div class="amount">
                <span class="{if $statement->amount > 0}positive{else}negative{/if}Amount">{#$statement->amount}</span>
            </div>
        </div>
    </li>
{/foreach}
