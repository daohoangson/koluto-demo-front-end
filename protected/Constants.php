<?php

class Constants
{
	const CACHE_UPDATED_TIMESTAMP = 'updatedTimestamp';
	const CACHE_SECTION_WORDS_X = 'sectionWords_%s';
	const CACHE_SECTION_WORDS_ALL_LATEST_X = 'allLatestSectionWords_%s';
	const CACHE_SECTION_WORDS_MAX = 50;
	const CACHE_FEATURE_ARTICLES = 'featureArticles';
	const CACHE_SECTION_ARTICLES_X = 'sectionArticles_%s';
	
	const SECTION_ALL = 'all';
	public static $SECTIONS = array(
		'culture',
		'education',
		'entertainment',
		'fashion',
		'finance',
		'game',
		'it',
		'lifestyle',
		'social',
		'sport'
	);
	public static $SECTION_NAMES = array(
		'culture' => 'Văn hóa',
		'education' => 'Giáo dục',
		'entertainment' => 'Giải trí',
		'fashion' => 'Thời trang',
		'finance' => 'Kinh tế, Tài chính',
		'game' => 'Game',
		'it' => 'Công nghệ',
		'lifestyle' => 'Đời sống',
		'social' => 'Xã hội',
		'sport' => 'Thể thao',
	);
}