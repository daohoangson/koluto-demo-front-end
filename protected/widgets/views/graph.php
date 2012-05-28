<div id="<?php echo $graphId; ?>" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px"></div>
<script type="text/javascript">
(function <?php echo $graphId; ?>_function(container)
{
	var
		wordsData = <?php echo json_encode($wordsData); ?>,
		ymdSections = <?php echo json_encode($ymdSections); ?>,
		d = [],
		i, j, graph;

	var xLabels = [];
	for (j = 0; j < ymdSections.length; j++)
	{
		xLabels.push(ymdSections[j]);
	}

	for (var word in wordsData)
	{
		wordD = {
			data: [],
			label: word
		};

		for (j = 0; j < ymdSections.length; j++)
		{
			var value = 0;
			
			if (wordsData[word][ymdSections[j]])
			{
				// data for this section exists
				value = wordsData[word][ymdSections[j]]
			}
			else
			{
				// no data
			}

			wordD.data.push([j, value]);
		}
		
		d.push(wordD);
	}

	// Draw Graph
	graph = Flotr.draw(container, d, {
		xaxis: {
			tickFormatter: function (x)
			{
				var x = parseInt(x);
				return xLabels[x % xLabels.length];
			}
		},
		yaxis: {
			max: 50
		}
	});
})(document.getElementById('<?php echo $graphId; ?>'));
</script>