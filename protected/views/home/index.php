<?php $this->pageTitle=Yii::app()->name; ?>

<div>
	<div style="width: 300px; float: left">
		<?php foreach ($feature as $article): ?>
			<?php $this->widget('application.widgets.ImageFromHtml', array(
				'html' => $article->article_html,
				'width' => 300,
			)); ?>
			<?php break; ?>
		<?php endforeach; ?>
	</div>
	<div style="margin-left: 310px">
		<?php foreach ($feature as $article): ?>
		<div>
			<h3><a href="<?php echo $article->article_source; ?>" target="_blank"><?php echo mb_substr(trim($article->article_text), 0, 60, 'utf-8'); ?>...</a></h3>
			<p><?php echo mb_substr(trim($article->article_text), 60, 300, 'utf-8'); ?>...</p>
		</div>
		<?php endforeach; ?>
	</div>
</div>

<div>
	<div style="width: 600px; float: left">
		<?php foreach ($sections as $section => $sectionArticles): ?>
			<?php if (!empty($sectionArticles)): ?>
				<h2><?php echo CHtml::link(Constants::$SECTION_NAMES[$section], array('section/view', 'section' => $section)) ?></h2>
				<div>
					<?php $isFirst = true; ?>
					<?php foreach ($sectionArticles as $article): ?>
						<?php if ($isFirst): ?>
							<div style="width: 200px; float: left">
								<h3><a href="<?php echo $article->article_source; ?>"><?php echo mb_substr(trim($article->article_text), 0, 30, 'utf-8'); ?>...</a></h3>
								<?php $this->widget('application.widgets.ImageFromHtml', array(
									'html' => $article->article_html,
									'width' => 200,
								)); ?>
								<p><?php echo mb_substr(trim($article->article_text), 30, 100, 'utf-8'); ?>...</p>
							</div>
							<div style="margin-left: 210px;">
						<?php else: ?>
								<div>
									<h4><a href="<?php echo $article->article_source; ?>"><?php echo mb_substr(trim($article->article_text), 0, 45, 'utf-8'); ?>...</a></h4>
									<p><?php echo mb_substr(trim($article->article_text), 45, 150, 'utf-8'); ?>...</p>
								</div>
						<?php endif; ?>
						<?php $isFirst = false; ?>
					<?php endforeach; ?>
					<?php if ($isFirst == false): ?>
							</div><!-- <div style="margin-left: 210px;"> -->
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<div style="margin-left: 610px;">
		<h2>Trending</h2>
		<ul>
			<?php foreach ($trendingWords as $word): ?>
			<li><?php echo CHtml::link($word, array('word/view', 'word' => $word)); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>