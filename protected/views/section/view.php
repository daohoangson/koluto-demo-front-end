<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/flotr2/flotr2.js'); ?>

<?php $this->pageTitle=Yii::app()->name; ?>

<h1><?php echo Constants::$SECTION_NAMES[$section]; ?></h1>
<div>
	<div style="width: 600px; float: left">
		<div id="graph" style="width: 600px; height: 400px"></div>
		<div>
			Trending: <?php echo implode(', ', $trendingWords); ?>
		</div>
	</div>
	<div style="margin-left: 610px;">
		<?php foreach ($articles as $article): ?>
			<div>
				<h3><a href="<?php echo $article->article_source; ?>"><?php echo mb_substr(trim($article->article_text), 0, 30, 'utf-8'); ?>...</a></h3>
				<p><?php echo mb_substr(trim($article->article_text), 30, 100, 'utf-8'); ?>...</p>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<script type="text/javascript">
(function basic_timeline(container)
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
console.log(d);
	// Draw Graph
	graph = Flotr.draw(container, d, {
		xaxis: {
			noTicks: 3,
			tickFormatter: function (x)
			{
				var x = parseInt(x);
				return xLabels[x % xLabels.length];
			}
		}, 
		yaxis: {
			showLabels : false
		},
		grid: {
			horizontalLines : false
		}
	});
})(document.getElementById("graph"));
</script>