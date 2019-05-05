<?php
namespace Zframework;
use Zframework\Controller;

class Middleware 
{
	
	function __construct($condition,$redirect)
	{
		$controller = new Controller;
		if(!$condition)
			$controller->redirect()->url($redirect);
	}
}