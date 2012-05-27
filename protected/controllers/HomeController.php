<?php

class HomeController extends Controller
{
	public function actionIndex()
	{
		$this->_checkForUpdate();
		
		$this->render('index');
	}
	
	protected function _checkForUpdate()
	{
		/* @var $cache CMemCache */
		$cache = Yii::app()->cache;
		
		/* @var $db CDbConnection */
		$db = Yii::app()->db;
		
		$article = Article::model();
		
		$updatedTimestamp = $cache->get(Constants::CACHE_UPDATED_TIMESTAMP);
		
		$latest = $article->findBySql("SELECT date_timestamp FROM tbl_article ORDER BY date_timestamp DESC LIMIT 1");
		$latestTimestamp = $latest->date_timestamp;
		
		if (($latestTimestamp + 0) > ($updatedTimestamp + 0))
		{
			// oops, we have to update the cache now...
			echo 'Updating caches...<br/>';
			
			
			
			die('Finished updating caches! Please refresh.');
		}
	}
}