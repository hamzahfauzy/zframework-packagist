<?php
namespace vendor\zframework\util;

class Request
{
	
	function __construct()
	{
		foreach ($_REQUEST as $key => $value) {
			$this->{$key} = $value;
			$_SESSION["old"][$key] = $value;
		}
		$_SESSION['old']['used'] = false;
	}
}