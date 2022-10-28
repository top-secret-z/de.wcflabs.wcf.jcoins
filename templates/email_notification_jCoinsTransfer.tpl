{if $mimeType === 'text/plain'}
{lang}{@$languageVariablePrefix}.mail.plaintext{/lang}

{@$event->getUserNotificationObject()->getMailText($mimeType)} {* this line ends with a space *}
{else}
    {lang}{@$languageVariablePrefix}.mail.html{/lang}
    {assign var='user' value=$event->getAuthor()}
    {assign var='statement' value=$event->getUserNotificationObject()}

    {if $notificationType == 'instant'}{assign var='avatarSize' value=48}
    {else}{assign var='avatarSize' value=32}{/if}
    {capture assign='statementContent'}
    <table cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td><a href="{link controller='User' object=$user isHtmlEmail=true}{/link}" title="{$statement->username}">{@$user->getAvatar()->getImageTag($avatarSize)}</a></td>
            <td class="boxContent">
                <div class="containerHeadline">
                    <h3>
                        {if $statement->userID}
                            <a href="{link controller='User' object=$user isHtmlEmail=true}{/link}">{$statement->username}</a>
                        {else}
                            {$statement->username}
                        {/if}
                        &#xb7;
                        <a href="{$statement->getLink()}"><small>{$statement->time|plainTime}</small></a>
                    </h3>
                </div>
                <div>
                    {@$statement->getMailText($mimeType)}
                </div>
            </td>
        </tr>
    </table>
    {/capture}
    {include file='email_paddingHelper' block=true class='box'|concat:$avatarSize content=$statementContent sandbox=true}
{/if}
