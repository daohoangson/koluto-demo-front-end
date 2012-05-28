<?php

require_once(dirname(__FILE__) . '/../3rdparty/koluto/api.php');

class Koluto extends CApplicationComponent
{
	public function getApi()
	{
		static $api = false;
		
		if ($api === false)
		{
			$api = new Koluto_Api();
			$api->setAuthInfo('sondh', '1'); // TODO: remove hardcode
		}
		
		return $api;
	}
}