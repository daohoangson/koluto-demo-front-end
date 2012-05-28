<?php

class HomeController extends Controller
{
	public function actionIndex()
	{
		// always check for update with index requests
		// TODO: move this to a cronjob or something
		$this->_checkForUpdate();
		
		/* @var $cache CMemCache */
		$cache = Yii::app()->cache;
		
		$feature = array();
		$sections = array();
		$updatedTimestamp = $cache->get(Constants::CACHE_UPDATED_TIMESTAMP);
		$ymdFromUpdatedTimestamp = date('Ymd', $updatedTimestamp);
		$ymdFromTheDayBeforeUpdatedTimestamp = date('Ymd', $updatedTimestamp - 86400);
		
		$trendingWords = $cache->get(sprintf(Constants::CACHE_SECTION_WORDS_X, Constants::SECTION_ALL));
		
		$feature = $cache->get(Constants::CACHE_FEATURE_ARTICLES);
		if (count($feature) > 3)
		{
			$feature = array_slice($feature, 0, 3);
		}
		
		foreach (Constants::$SECTIONS as $section)
		{
			$sections[$section] = $cache->get(sprintf(Constants::CACHE_SECTION_ARTICLES_X, $section));
			if (count($sections[$section]) > 4)
			{
				$sections[$section] = array_slice($sections[$section], 0, 4);
			}
		}
		
		$data = array(
			'feature' => $feature,
			'sections' => $sections,
			'trendingWords' => $trendingWords,
		);
		
		$this->render('index', $data);
	}
	
	protected function _getArticlesWithWords(array $words, array $sections = array())
	{
		/* @var kolutoApi Koluto_Api */
		$kolutoApi = Yii::app()->koluto->api;
		
		$documentIds = $kolutoApi->getDocumentIdsWithWords($words, $sections);
		$articles = array();
		
		foreach ($documentIds as $documentId)
		{
			$article = Article::model()->find('koluto_id=:id', array(':id' => $documentId));
			
			if (!empty($article)) 
			{
				$articles[$documentId] = $article;
			} 
		}
		
		return $articles;
	}
	
	protected function _checkForUpdate()
	{
		/* @var $cache CMemCache */
		$cache = Yii::app()->cache;
		
		/* @var kolutoApi Koluto_Api */
		$kolutoApi = Yii::app()->koluto->api;
		
		$article = Article::model();
		
		$updatedTimestamp = $cache->get(Constants::CACHE_UPDATED_TIMESTAMP);
		
		$latest = $article->findBySql("SELECT date_timestamp FROM tbl_article ORDER BY date_timestamp DESC LIMIT 1");
		$latestTimestamp = $latest->date_timestamp;
		
		if (($latestTimestamp + 0) > ($updatedTimestamp + 0)
			OR isset($_GET['update'])
		)
		{
			// oops, we have to update the cache now...
			echo 'Updating caches...<br/>';
			$start = microtime(true);
			
			$appWords = $kolutoApi->getWords();
			$sectionWords = array();
			$sectionWordsLatest = array();
			$sectionWordsAlmostLatest = array();
			$sectionFinalWords = array();
			
			// get section words
			foreach (Constants::$SECTIONS as $section)
			{
				$sectionWords[$section] = $kolutoApi->getSectionWords($section);
			}
			$sectionWords[Constants::SECTION_ALL] = array();
			
			// get section words today
			$ymdFromLatestTimestamp = date('Ymd', $latestTimestamp);
			foreach (Constants::$SECTIONS as $section)
			{
				$sectionWordsLatest[$section] = $kolutoApi->getSectionWords($section . '_' . $ymdFromLatestTimestamp);
			}
			$sectionWordsLatest[Constants::SECTION_ALL] = $kolutoApi->getSectionWords($ymdFromLatestTimestamp);
			
			// gets section words yesterday
			$ymdFromTheDayBeforeLatestTimestamp = date('Ymd', $latestTimestamp - 86400);
			foreach (Constants::$SECTIONS as $section)
			{
				$sectionWordsAlmostLatest[$section] = $kolutoApi->getSectionWords($section . '_' . $ymdFromTheDayBeforeLatestTimestamp);
			}
			$sectionWordsAlmostLatest[Constants::SECTION_ALL] = $kolutoApi->getSectionWords($ymdFromTheDayBeforeLatestTimestamp);
			
			// merge them all and filter out common words
			foreach (array_keys($sectionWordsLatest) as $section)
			{
				$merged = array_merge($sectionWordsLatest[$section], $sectionWordsAlmostLatest[$section]);
				$merged = array_unique($merged);
				$sectionFinalWords[$section] = array();
				$tmp = array();
				
				foreach ($merged as $word)
				{
					if (in_array($word, $sectionWords[$section]) OR in_array($word, $appWords))
					{
						// this word is too common
						// do not add
					}
					else 
					{
						// looks good, add to the tmp list
						$tmp[] = $word;
					}
				}
				
				// now get the top results from latest and almost latest
				$i = 0;
				while (count($sectionFinalWords[$section]) < Constants::CACHE_SECTION_WORDS_MAX)
				{
					if (!isset($sectionWordsLatest[$section][$i]) OR !isset($sectionWordsAlmostLatest[$section][$i]))
					{
						break;
					}
					
					if (in_array($sectionWordsLatest[$section][$i], $tmp)
						AND !in_array($sectionWordsLatest[$section][$i], $sectionFinalWords[$section]))
					{
						$sectionFinalWords[$section][] = $sectionWordsLatest[$section][$i];
					}
					
					if (in_array($sectionWordsAlmostLatest[$section][$i], $tmp)
						AND !in_array($sectionWordsAlmostLatest[$section][$i], $sectionFinalWords[$section]))
					{
						$sectionFinalWords[$section][] = $sectionWordsAlmostLatest[$section][$i];
					}
					
					$i++;
				}
			}
			
			// save to cache
			foreach ($sectionFinalWords as $section => $words)
			{
				$cache->set(sprintf(Constants::CACHE_SECTION_WORDS_X, $section), $words);
			}
			
			// also save latest section words
			foreach ($sectionWordsLatest as $section => $words)
			{
				$cache->set(sprintf(Constants::CACHE_SECTION_WORDS_ALL_LATEST_X, $section), $words);
			}
			
			// start prepare articles
				// feature articles
				$featureArticles = $this->_getArticlesWithWords(
					$sectionFinalWords[Constants::SECTION_ALL],
					array($ymdFromLatestTimestamp)
				);
				$cache->set(Constants::CACHE_FEATURE_ARTICLES, $featureArticles);
				
				// section articles
				foreach (Constants::$SECTIONS as $section)
				{
					$cache->set(
						sprintf(Constants::CACHE_SECTION_ARTICLES_X, $section),
						$this->_getArticlesWithWords(
							$sectionFinalWords[$section],
							array($section . '_' . $ymdFromLatestTimestamp)
						)
					);
				}
			
			// save cache's update timestamp
			$cache->set(Constants::CACHE_UPDATED_TIMESTAMP, $latestTimestamp);
			
			$finish = microtime(true);
			echo 'Time: ', ($finish - $start), '<br/>';
			die('Finished updating caches! Please refresh.');
		}
	}
}