<?php
namespace Zframework;

class Controller
{
	public $is_redirect = false;
	public $page = "";
	public $param = [];

	function __construct()
	{
		$this->view = new View();
		$this->base_url = base_url();
		$this->view->base_url = $this->base_url;
	}

	function __destruct()
	{
		if($this->is_redirect)
		{
			$param = "";
			if(!empty($this->param))
			{
				$param .= "?";
				foreach ($this->param as $key => $value) {
					$param .= $key."=".$value."&";
				}
			}
			header("location:".$this->base_url.$this->page.$param);
		}
	}

	function middleware($name)
	{		
		$name = "app\\middleware\\".$name;
		return new $name;
	}
	
	function redirect(){
		$this->is_redirect = true;
		return $this;
	}

	function url($route,$param = false)
	{
		if(is_array($param))
		{
			$this->param = $param;
		}
		$this->page = $route;
	}
}
