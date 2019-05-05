<?php
namespace vendor\zframework;

class Session 
{
	static $usersclass = "app\User";
	static $user_session_key = "id";

	public static function init()
	{
		session_start();
	}

	public static function destroy()
	{
		session_destroy();
	}

	public static function set($key,$value)
	{
		$_SESSION[$key] = $value;
	}

	public static function reset($key)
	{
		unset($_SESSION[$key]);
	}

	public static function get($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
	}

	public static function user()
	{
		if(!self::get(self::$user_session_key))
		{
			return false;
		}
		$userclass = new self::$usersclass;
		return $userclass->where(self::$user_session_key,self::get(self::$user_session_key))->first();
	}

}