<ul class="sidebarItemList">
    {foreach from=$boxUsers item=boxUser}
        <li class="box24">
            <a href="{link controller='User' object=$boxUser}{/link}" aria-hidden="true">{@$boxUser->getAvatar()->getImageTag(24)}</a>

            <div class="sidebarItemTitle">
                <h3>{user object=$boxUser}</h3>
                <small>{#$boxUser->jCoinsAmount} {lang}{JCOINS_NAME}{/lang}</small>
            </div>
        </li>
    {/foreach}
</ul>
