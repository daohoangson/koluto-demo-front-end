<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/flotr2/flotr2.js'); ?>

<?php $this->pageTitle=Yii::app()->name; ?>

<h1><?php echo Constants::$SECTION_NAMES[$section]; ?></h1>
<div>
	<div style="width: 600px; float: left">
		<?php $this->widget('application.widgets.Graph', array(
			'wordsData' => $wordsData,
			'ymdSections' => $ymdSections,
			'width' => 600,
			'height' => 400,
		)); ?>
		<div>
			Trending:
			<?php foreach ($trendingWords as $word): ?>
				<?php echo CHtml::link($word, array('word/view', 'word' => $word)); ?>
			<?php endforeach; ?>
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
</script>