<?php
namespace vendor\zframework\util;

class Assets
{
	public static function get($src)
	{
		return base_url()."/".$src;
	}
}