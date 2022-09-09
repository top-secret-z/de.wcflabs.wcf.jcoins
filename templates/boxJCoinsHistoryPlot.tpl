<div><p>{lang}wcf.jcoins.statement.balance{/lang}</p><br></div>

<div id="chart" style="width: 280px; height: 140px">

</div>
<div id="chartTooltip" class="balloonTooltip active" style="display: none;"></div>

<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/flot/jquery.flot.js"></script>
<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/flot/jquery.flot.time.js"></script>
<script data-relocate="true">
	require(['Language', 'StringUtil'], function(Language, StringUtil) {
		Language.addObject({
			'wcf.acp.stat.timeFormat.daily': '{jslang}wcf.acp.stat.timeFormat.daily{/jslang}',
			'wcf.jcoins.title': '{capture assign=jCoinsTitle}{jslang}wcf.jcoins.title{/jslang}{/capture}{@$jCoinsTitle|encodeJS}'
		});
		
		var options = {
			series: {
				lines: {
					show: true
				},
				points: {
					show: true
				}
			},
			grid: {
				hoverable: true
			},
			xaxis: {
				mode: "time",
				minTickSize: [1, "day"],
				timeFormat: Language.get('wcf.acp.stat.timeFormat.daily'),
				monthNames: Language.get('__monthsShort')
			},
			yaxis: {
				min: {@$minValue},
				tickDecimals: 0,
				tickFormatter: function(val) {
					return StringUtil.addThousandsSeparator(val);
				}
			},
		};

		$.plot("#chart", [
			[
				{foreach from=$dataArray item="data" key="key"}
				{assign var="time" value="$key * 1000"}
				[{$time}, {$data}],
				{/foreach}
			]
		], options);
		
		{literal}
		$("#chart").on("plothover", function(event, pos, item) {
			if (item) {
				$("#chartTooltip").html(item.series.xaxis.tickFormatter(item.datapoint[0], item.series.xaxis) + ', ' + StringUtil.formatNumeric(item.datapoint[1]) + ' ' + Language.get('wcf.jcoins.title')).css({top: item.pageY + 5, left: item.pageX + 5}).wcfFadeIn();
			}
			else {
				$("#chartTooltip").hide();
			}
		});
		{/literal}
	});
</script>
