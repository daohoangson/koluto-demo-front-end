<?php

class Koluto_Api
{
	protected $_rootUrl = '';
	protected $_userName = '';
	protected $_password = '';
	
	public static $CURL_OPTS = array(
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT        => 60,
		CURLOPT_USERAGENT      => 'koluto-php-1.0',
	);
	
	public function __construct($host = '127.0.0.1', $port = 29690)
	{
		$this->_rootUrl = sprintf("http://%s:%d", $host, $port);
	}
	
	public function setAuthInfo($userName, $password)
	{
		$this->_userName = $userName;
		$this->_password = $password;
	}
	
	public function getDocumentIdsWithWords(array $words, array $sections = array())
	{
		$url = $this->_rootUrl . '/search';
		$response = $this->makeRequest($url, 'POST', array('sections' => $sections, 'words' => $words));
		
		$documentIds = array();
		
		foreach ($response as $document) {
			$documentIds[] = $document['_id'];
		}
		
		return $documentIds;
	}
	
	public function getWords()
	{
		$url = $this->_rootUrl . '/words';
		$response = $this->makeRequest($url);
		
		if (!empty($response) AND isset($response['words']))
		{
			return $response['words'];
		}
		else
		{
			return array();
		}
	}
	
	public function getSectionWords($section)
	{
		$url = $this->_rootUrl . '/sections/' . rawurlencode($section);
		$response = $this->makeRequest($url);
		
		if (!empty($response) AND isset($response['section']) AND isset($response['section']['words']))
		{
			return $response['section']['words'];
		}
		else
		{
			return array();
		}
	}
	
	public function getWord($word)
	{
		$url = $this->_rootUrl . '/words/' . rawurlencode($word);
		$response = $this->makeRequest($url);
		
		if (!empty($response) AND isset($response['word']))
		{
			return $response['word'];
		}
		else
		{
			return array();
		}
	}
	
	protected function makeRequest($url, $method = 'GET', $params = array())
	{
		$ch = curl_init();
		
		$opts = self::$CURL_OPTS;
		
		if (!isset($params['_responseFormat']))
		{
			$params['_responseFormat'] = 'json';
		}
		
		if ($method == 'GET')
		{
			$url .= ((strpos($url, '?') === false) ? ('?') : ('&')) . http_build_query($params, null, '&');
		}
		else
		{
			$opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
		}
		
		$opts[CURLOPT_URL] = $url;
		
		$opts[CURLOPT_USERPWD] = sprintf('%s:%s', $this->_userName, $this->_password);
		
		curl_setopt_array($ch, $opts);
		$result = curl_exec($ch);
		
		curl_close($ch);
		
		if ($result !== false && $params['_responseFormat'] == 'json')
		{
			// parse json automatically
			$result = json_decode($result, true);
		}
		
		return $result;
	}
}