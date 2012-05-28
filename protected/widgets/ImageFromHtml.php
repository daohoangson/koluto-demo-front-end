<?php

class ImageFromHtml extends CWidget
{
	public $html;
	public $width = 'auto';
	
	public function run()
	{
		$img = false;
		
		if (preg_match('/<img.+\/>/i', $this->html, $matches)) 
		{
			if (preg_match('/\ssrc="([^"]+)"/i', $matches[0], $matches2))
			{
				$img = $matches2[1];
			}
		}
		
		if (empty($img))
		{
			echo 'No image';
		}
		else 
		{
			echo sprintf('<img src="%s" width="%s" />', $img, $this->width);
		}
	}
}