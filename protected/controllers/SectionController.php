<?php

class SectionController extends Controller
{
	public function actionView($section)
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
		
		$articles = $cache->get(sprintf(Constants::CACHE_SECTION_ARTICLES_X, $section));
		
		$wordsDataRaw = array();
		$wordsData = array();
		$ymdSections = array();
		$wordCount = 0;
		foreach ($trendingWords as $word)
		{
			if ($wordCount > 10) break; // TODO: remove hardcode
			
			$wordsDataRaw[$word] = $kolutoApi->getWord($word);
			
			$wordCount++;
		}
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
				$sectionCode = sprintf('s_%s_%s', $section, $ymdSection);
				if (isset($dataRaw[$sectionCode]))
				{
					$wordsData[$word][$ymdSection] = $dataRaw[$sectionCode];
				}
			}
		}
		
		$data = array(
			'section' => $section,
			'articles' => $articles,
			'trendingWords' => $trendingWords,
			'wordsData' => $wordsData,
			'ymdSections' => $ymdSections,
		);
		
		$this->render('view', $data);
	}
}