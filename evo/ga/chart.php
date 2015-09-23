<?php

require_once('../../lib/php/classes/googlecharts_1.02.php');

function add_data_for_chart($new_data){
	echo "<script src='../../lib/js/underscore-min.js'></script>
	<script src='../../lib/js/jquery/jquery.js'></script>
	<script src='../../lib/js/jquery/!data_visualization/flot/flot-0.7/jquery.flot.js'></script>
	<script src='../../lib/js/jquery/!data_visualization/flot/flot-0.7/jquery.flot.crosshair.js'></script>
	<script>
		function include(arr, obj){
			return (arr.indexOf(obj) != -1);
		}
		$(function(){
			var incorrect_data = JSON.parse($('#chart_data').html());
			var data = [
				{label:'strength = -0.00',data:[]},
				{label:'dexterity = -0.00',data:[]},
				{label:'resistance = -0.00',data:[]},
				{label:'intelligence = -0.00',data:[]}
			];
			i = 0;
			for(params in incorrect_data){
				params = incorrect_data[params];
				//console.info(params);
				for(param in params){ //console.info(param);
					for(d in data)
						if(data[d].label.indexOf(param) != -1)
							data[d].data[data[d].data.length] = [i, params[param]];
				}
				i++;
			}
			console.info(data);
			var options = {};
			
			var options = {
				series: { lines: { show: true }, shadowSize: 0 },
				crosshair: { mode: 'x' },
				grid: { hoverable: true, autoHighlight: false },
				pan: {
					interactive: true
				},
				legend: {
					position: 'nw'
				}
			};

			plot = $.plot($('#chart_place'), data, options);
			
			var legends = $('#chart_place .legendLabel');
			
			var updateLegendTimeout = null;
			var latestPosition = null;
			
			function updateLegend() {
				updateLegendTimeout = null;
				
				var pos = latestPosition;
				
				var axes = plot.getAxes();
				if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max || pos.y < axes.yaxis.min || pos.y > axes.yaxis.max)
					return;

				var i, j, dataset = plot.getData();
				for (i = 0; i < dataset.length; ++i) {
					var series = dataset[i];

					// find the nearest points, x-wise
					for (j = 0; j < series.data.length; ++j)
						if (series.data[j][0] > pos.x)
							break;
					
					// now interpolate
					var y, p1 = series.data[j - 1], p2 = series.data[j];
					if (p1 == null)
						y = p2[1];
					else if (p2 == null)
						y = p1[1];
					else
						y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);

					legends.eq(i).text(series.label.replace(/=.*/, '= ' + y.toFixed(2)));
				}
			}
    
			$('#chart_place').bind('plothover', function (event, pos, item){
				latestPosition = pos;
				if (!updateLegendTimeout)
					updateLegendTimeout = setTimeout(updateLegend, 50);
			});
		});
	</script>
	<div id='chart_data' style='display:none'>".json_encode($new_data)."</div>
	<div id='chart_place' style='width:500;height:300'></div>
	<p id='hoverdata'></p>";
}

?>