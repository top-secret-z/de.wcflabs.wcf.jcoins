<button class="small jCoinsPurchaseButton" data-token="{$token}" id="jCoinsPurchaseButton-{$buttonUID}">{lang}wcf.payment.jcoins.button.purchase{/lang}</button>

<script data-relocate="true">
    require(['UZ/JCoins/Payment', 'WoltLabSuite/Core/Template', 'Language'], function(UZJCoinsPayment, Template, Language) {
        Language.addObject({
            'wcf.payment.jcoins.overlay.title': '{jslang}wcf.payment.jcoins.overlay.title{/jslang}',
            'wcf.jcoins.title': '{jslang}wcf.jcoins.title{/jslang}'
        });

        new UZJCoinsPayment(new Template('{@$template|encodeJS}'), '{$jCoinsAmount|encodeJS}', '{$realCost|encodeJS}', '{$currency|encodeJS}', '{$cost|encodeJS}', '{$cost|currency|encodeJS}', '{$name|encodeJS}', '{$buttonUID|encodeJS}', '{$token|encodeJS}', '{$returnURL|encodeJS}')
    });
</script>
