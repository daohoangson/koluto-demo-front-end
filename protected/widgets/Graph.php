<?php

class Graph extends CWidget
{
	public $wordsData = array();
	public $ymdSections = array();
	public $width = 600;
	public $height = 400;
	
	public function run()
	{
		static $counter = 0;
		
		$counter++;
		$graphId = sprintf('graph_%d', $counter);
		
		echo $this->render('graph', array(
			'wordsData' => $this->wordsData,
			'ymdSections' => $this->ymdSections,
			'width' => $this->width,
			'height' => $this->height,
		
			'graphId' => $graphId,
		));
	}
}