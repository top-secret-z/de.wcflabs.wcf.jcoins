<ul class="sidebarItemList">
    {foreach from=$topUser item=user}
        <li class="box24">
            <a href="{link controller='User' object=$user}{/link}" aria-hidden="true">{@$user->getAvatar()->getImageTag(24)}</a>

            <div class="sidebarItemTitle">
                <h3>{user object=$user}</h3>
                <small>{#$user->jCoinsAmount} {JCOINS_NAME}</small>
            </div>
        </li>
    {/foreach}
</ul>
