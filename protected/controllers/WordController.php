<?php

class WordController extends Controller
{
	public function actionView($word)
	{
		/* @var $cache CMemCache */
		$cache = Yii::app()->cache;
		
		/* @var kolutoApi Koluto_Api */
		$kolutoApi = Yii::app()->koluto->api;
		
		$articles = array();
		$updatedTimestamp = $cache->get(Constants::CACHE_UPDATED_TIMESTAMP);
		$ymdFromUpdatedTimestamp = date('Ymd', $updatedTimestamp);
		$ymdFromTheDayBeforeUpdatedTimestamp = date('Ymd', $updatedTimestamp - 86400);
		
		$trendingWords = $cache->get(sprintf(Constants::CACHE_SECTION_WORDS_X, $section));
		
		$articles = $this->_getArticlesWithWords(array($word), array($ymdFromUpdatedTimestamp));
		
		$wordsDataRaw = array(
			$word => $kolutoApi->getWord($word),
		);
		$wordsData = array();
		$ymdSections = array();
		for ($i = 0; $i < 5; $i++)
		{
			// calculate $ymdSections
			// TODO: remove hardcode
			$ymdSections[] = date('Ymd', $updatedTimestamp - $i * 86400);
		}
		$ymdSections = array_reverse($ymdSections);
		foreach ($wordsDataRaw as $word => $dataRaw)
		{
			$wordsData[$word] = array();
			
			foreach ($ymdSections as $ymdSection)
			{
				$sectionCode = sprintf('s_%s', $ymdSection);
				if (isset($dataRaw[$sectionCode]))
				{
					$wordsData[$word][$ymdSection] = $dataRaw[$sectionCode];
				}
			}
		}
		
		$data = array(
			'word' => $word,
			'articles' => $articles,
			'wordsData' => $wordsData,
			'ymdSections' => $ymdSections,
		);
		
		$this->render('view', $data);
	}
	
	protected function _getArticlesWithWords(array $words, array $sections = array())
	{
		// TODO: do no copy from HomeController
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
}