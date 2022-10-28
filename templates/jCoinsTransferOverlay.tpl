<div id="transferOverlay">
    <div>
        <section id="transferOverlayGeneralFieldset">
            <dl id="transferOverlayReceiverDl">
                <dt><label for="receiverInput">{lang}wcf.jcoins.transfer.receiver{/lang}</label></dt>
                <dd>
                    <input type="text" id="receiverInput" class="long jsDialogAutoFocus" name="receiver" value="" />
                    <small>{lang}wcf.jcoins.transfer.description{/lang}</small>
                </dd>
            </dl>
            <dl id="transferOverlayAmountDl">
                <dt><label for="amountInput">{lang}wcf.jcoins.transfer.amount{/lang}</label></dt>
                <dd>
                    <input type="number" id="amountInput" name="amount" value="0" {if !$__wcf->session->getPermission('mod.jcoins.canTransferModerative')}min="1" max="{$__wcf->user->jCoinsAmount}" {/if}class="short" />
                    {if $transferFee != 0 && $__wcf->session->getPermission('user.jcoins.mustPayTransferFee')}
                        <small>{lang}wcf.jcoins.transfer.fee.description{/lang}</small>
                    {/if}
                </dd>
            </dl>
            <dl id="transferOverlayReasonDl">
                <dt><label for="reasonInput">{lang}wcf.jcoins.transfer.reason{/lang}</label></dt>
                <dd>
                    <input type="text" id="reasonInput" name="reason" maxlength="255" class="long" />
                </dd>
            </dl>

            {if $__wcf->session->getPermission('mod.jcoins.canTransferModerative')}
                <dl id="transferOverlayModerative">
                    <dt></dt>
                    <dd>
                        <label><input type="checkbox" name="moderative" id="moderativeInput" value="1" /> {lang}wcf.jcoins.transfer.moderative{/lang}</label>
                        <small>{lang}wcf.jcoins.transfer.moderative.description{/lang}</small>
                    </dd>
                </dl>
            {/if}
        </section>
    </div>

    <div class="formSubmit">
        <button class="jsTransferSubmit buttonPrimary" accesskey="s">{lang}wcf.global.button.submit{/lang}</button>
        <button class="jsTransferCancel" accesskey="s">{lang}wcf.global.button.cancel{/lang}</button>
    </div>
</div>

{* user search for input *}
<script data-relocate="true">
    new WCF.Search.User('#receiverInput', null, false, null, true);
</script>
