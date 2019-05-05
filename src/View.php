<?php
namespace Zframework;
use Zframework\util\Assets;

class View
{
	public $file = "";
	public $data = [];
	public $is_render = false;
	function __construct()
	{
		$this->assets = new Assets;
	}

	function __destruct()
	{
		if($this->is_render)
		{
			extract($this->data);
			require '../views/'.$this->file.'.php';
			if(Session::get('old'))
				$_SESSION['old']['used'] = true;
		}
	}

	function load($file)
	{
		$file = str_replace(".", "/", $file);
		require '../views/'.$file.'.php';
	}

	function route($page)
	{
		return $this->base_url.$page;
	}

	function render($file){
		$this->is_render = 1;
		$file = str_replace(".", "/", $file);
		$this->file = $file;
		return $this;
	}

	function with($key, $value = false)
	{
		if(is_array($key)){
			$this->data = $key;
			return;
		}

		$this->data[$key] = $value;
		return;
	}


}