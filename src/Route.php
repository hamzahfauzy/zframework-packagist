<?php
namespace Zframework;

class Route
{
	public static $_get = [];
	public static $_post = [];
	public static $base_prefix;
	public static $_namespace;
	public static $_middleware = "";

	public static function prefix($base_prefix)
	{
		self::$base_prefix = $base_prefix;
		return new static;
	}

	public static function returnGet()
	{
		return self::$_get;
	}

	public static function namespaces($name)
	{
		self::$_namespace = $name."\\";
		return new static;
	}

	public static function middleware($name)
	{
		self::$_middleware = $name;
		return new static;
	}

	public static function group($callback)
	{
		call_user_func($callback);
		self::$_middleware = "";
		self::$_namespace = "";
		self::$base_prefix = "";
		return;
	}

	public static function get($url,$controller)
	{

		if(!empty(self::$base_prefix))
			$url = self::$base_prefix . $url;
		if($url != "/") $url = rtrim($url,"/");
		$url = str_replace("{","(?'",$url);
		$url = str_replace("}","'[^/]+)",$url);
		$key = count(self::$_get);
		self::$_get[$key]["url"] = $url;
		self::$_get[$key]["controller"] = is_string($controller) ? self::$_namespace.$controller : $controller;
		self::$_get[$key]["middleware"] = self::$_middleware;
	}

	public static function post($url,$controller)
	{
		if(!empty(self::$base_prefix))
			$url = self::$base_prefix . $url;
		if($url != "/") $url = rtrim($url,"/");
		$key = count(self::$_post);
		self::$_post[$key]["url"] = $url;
		self::$_post[$key]["controller"] = is_string($controller) ? self::$_namespace.$controller : $controller;
		self::$_post[$key]["middleware"] = self::$_middleware;
	}

	public static function fetchGet($uri)
	{
		$return = new \stdClass();
		foreach (self::$_get as $key => $value) {
			if ( preg_match( '~^'.$value['url'].'$~i', $uri, $params ) ) {
				if(is_string($value["controller"]))
				{
					$arr = explode("@", $value["controller"]);
					$arr[0] = "app\\controllers\\".$arr[0];
					$return->className = $arr[0];
					$return->method = $arr[1];
				}else{
					$return->callback = $value["controller"];
				}
				$return->middleware = $value["middleware"];
				if(!empty($params))
					$return->param = $params;
				break;
		    }
		}
		return $return;
	}

	public static function fetchPost($uri)
	{
		$return = new \stdClass();
		foreach (self::$_post as $key => $value) {
			if($value['url'] == $uri){
				if(is_string($value["controller"]))
				{
					$arr = explode("@", $value["controller"]);
					$arr[0] = "app\\controllers\\".$arr[0];
					$return->className = $arr[0];
					$return->method = $arr[1];
				}else{
					$return->callback = $value["controller"];
				}
				$return->middleware = $value["middleware"];
				break;
			}
		}
		return $return;
	}
}