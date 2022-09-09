<dl{if $errorField == 'amount'} class="formError"{/if}>
	<dt><label for="amount">{lang}wcf.jcoins.transfer.amount{/lang}</label></dt>
	<dd>
		<input type="number" id="amount" name="amount" value="{$amount}" class="medium" />
		{if $errorField == 'amount'}
			<small class="innerError">
				{lang}wcf.global.form.error.empty{/lang}
			</small>
		{/if}
	</dd>
</dl>

<dl{if $errorField == 'reason'} class="formError"{/if}>
	<dt><label for="reason">{lang}wcf.jcoins.transfer.reason{/lang}</label></dt>
	<dd>
		<input type="text" id="reason" name="reason" value="{$reason}" class="medium" />
		{if $errorField == 'reason'}
			<small class="innerError">
				{lang}wcf.global.form.error.empty{/lang}
			</small>
		{/if}
	</dd>
</dl>

{if $transferID != 0}
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.ACP.Worker('transfer', 'wcf\\system\\worker\\JCoinsTransferWorker', '', {
				transferID: {@$transferID}
			});
		});
		//]]>
	</script>
{/if}
