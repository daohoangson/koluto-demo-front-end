<?php

class Article extends CActiveRecord
{
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
		return 'tbl_article';
	}
	
	public function primaryKey()
	{
		return 'article_id';
	}
}