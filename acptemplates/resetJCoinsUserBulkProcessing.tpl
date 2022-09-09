{if $resetID != 0}
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.ACP.Worker('reset', 'wcf\\system\\worker\\JCoinsResetWorker', '', {
				resetID: {@$resetID}
			});
		});
		//]]>
	</script>
{/if}