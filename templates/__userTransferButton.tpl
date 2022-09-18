{if MODULE_JCOINS && $user->userID != $__wcf->user->userID && $__wcf->session->getPermission('user.jcoins.canTransfer') && $__wcf->session->getPermission('user.jcoins.canUse')}
    <li id="transferButton"><a href="#">{lang}wcf.jcoins.transfer.title{/lang}</a></li>
    <script data-relocate="true">
        require(['UZ/JCoins/Transfer', 'StringUtil'], function(UZJCoinsTransfer) {
            new UZJCoinsTransfer('transferButton', '{$user->username|encodeJS}');
        });
    </script>
{/if}
