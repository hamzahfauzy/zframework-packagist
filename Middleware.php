<?php
namespace vendor\zframework;
use vendor\zframework\Controller;

class Middleware 
{
	
	function __construct($condition,$redirect)
	{
		$controller = new Controller;
		if(!$condition)
			$controller->redirect()->url($redirect);
	}
}