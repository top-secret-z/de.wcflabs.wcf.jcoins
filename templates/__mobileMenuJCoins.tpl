{if MODULE_JCOINS && $__wcf->user->userID && $__wcf->session->getPermission('user.jcoins.canUse')}
    <li class="menuOverlayItem" data-more="de.wcflabs.wcf.jcoins.UserMenuMobilePanel">
        <a href="{link controller='JCoinsStatementList'}{/link}" class="menuOverlayItemLink menuOverlayItemBadge box24" data-badge-identifier="jcoinsAmount">
            <span class="icon icon24 fa-money"></span>
            <span class="menuOverlayItemTitle">{lang}wcf.jcoins.title{/lang}</span>
            <span class="badge">{#$__wcf->user->jCoinsAmount}</span>
        </a>
    </li>
{/if}
