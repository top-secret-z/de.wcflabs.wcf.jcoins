{*
 * This file is located in the footer, because it must be the last JavaScript which is executed ;
 * because we unbind some buttons
 *}
{if $templateName == 'conversation' && $hasEnougthJCoins|isset && !$hasEnougthJCoins}
    <script data-relocate="true">
        require(['Language'], function(Language) {
            Language.addObject({
                'wcf.jcoins.amount.conversationAnswer.tooLow': '{jslang}wcf.jcoins.amount.conversationAnswer.tooLow{/jslang}'
            });

            elBySel('#messageQuickReply').innerHTML = '<p class="info">'+ Language.get('wcf.jcoins.amount.conversationAnswer.tooLow') +'</p>'
        });
    </script>
{/if}
