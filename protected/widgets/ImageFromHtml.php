<?php

class ImageFromHtml extends CWidget
{
	public $html;
	public $url;
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
			$imgInfo = parse_url($img);
			if (empty($imgInfo['host']) AND !empty($this->url))
			{
				// this is a relative url
				// we have to make it into an absolute url
				$urlInfo = parse_url($this->url);
				
				$newImgInfo = $urlInfo;
				if (isset($newImgInfo['query'])) unset($newImgInfo['query']);
				if (isset($newImgInfo['fragment'])) unset($newImgInfo['fragment']);
				
				if (substr($imgInfo['path'], 0, 1) == '/')
				{
					// this img url is relative to root
					$newImgInfo['path'] = $imgInfo['path'];
				}
				else 
				{
					// nope, it's relative to the current directory of the url
					// however, if baseurl is used in HTML, we are doomed
					$newImgInfo['path'] = dirname($urlInfo['path']) . '/' . $imgInfo['path']; // TODO: check to make sure it works with http://domain.com/something/ urls
				}
				
				if (isset($imgInfo['query']))
				{
					$newImgInfo['query'] = $imgInfo['query'];
				}
				
				$img = $newImgInfo['scheme'] . '://'
					. (($newImgInfo['user'] AND $newImgInfo['pass']) ? sprint('%s:%s@', $newImgInfo['user'], $newImgInfo['pass']) : '')
					. $newImgInfo['host']
					. $newImgInfo['path']
					. ($newImgInfo['query'] ? sprintf('?%s', $newImgInfo['query']) : '');
			}
			
			echo sprintf('<img src="%s" width="%s" />', $img, $this->width);
		}
	}
}