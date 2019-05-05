<?php
namespace Zframework;
use Zframework\util\QueryBuilder;

class Model 
{
	public static $_tbl;
	public static $_fields;
	public static $QueryBuilder;
	public static $_orderby = "";
	public static $_limit = "";
	public static $where_queue = [];

	function __construct()
	{
		self::init();
	}

	public static function findParam($name,$value,$class)
	{
		self::init();
		$rows = self::$QueryBuilder->select(self::$_tbl)->where($name,$value)->run(1);
		$obj = new $class;
		if(!empty($rows))
			foreach ($rows as $key => $value) {
				$obj->{$key} = $value;
			}
		return $obj;
	}

	public static function init()
	{
		self::$QueryBuilder = new QueryBuilder;
		self::$_tbl = isset(static::$table) ? static::$table : get_called_class();
		self::$_fields = isset(static::$fields) ? static::$fields : "";
	}

	public static function get()
	{
		self::init();
		self::$QueryBuilder->select(self::$_tbl);
		if(count(self::$where_queue))
		{
			foreach (self::$where_queue as $key => $value) {
				if($value['type'] == "AND")
				{
					if(is_array($value['value']))
				    {
				        self::$QueryBuilder->where($value['key'],$value['value'][0],$value['value'][1]);
				    }
				    else
				    {
				        self::$QueryBuilder->where($value['key'],$value['value']);
				    }
				}

				if($value['type'] == "OR")
				{
					if(is_array($value['value']))
				    {
				        self::$QueryBuilder->orwhere($value['key'],$value['value'][0],$value['value'][1]);
				    }
				    else
				    {
				        self::$QueryBuilder->orwhere($value['key'],$value['value']);
				    }
				}
			}
		}
		if(self::$_orderby != "")
		{
		    self::$QueryBuilder->orderby(self::$_orderby);
		}
		
		if(self::$_limit != "")
		{
		    self::$QueryBuilder->setlimit(self::$_limit);
		}
		self::$where_queue = [];
		self::$_orderby = "";
		self::$_limit = "";
		$data = self::$QueryBuilder->run();
		if(empty($data))
			return $data;
		$modelName = get_called_class();
		$model = [];
		foreach ($data as $key => $value) {
			$model[$key] = new $modelName;
			foreach ($value as $k => $val) {
				$model[$key]->{$k} = $val;
			}
		}

		return $model;
	}

	public static function first()
	{
		self::init();
		self::$QueryBuilder->select(self::$_tbl);
		if(count(self::$where_queue))
		{
			foreach (self::$where_queue as $key => $value) {
				if($value['type'] == "AND")
				{
					if(is_array($value['value']))
				    {
				        self::$QueryBuilder->where($value['key'],$value['value'][0],$value['value'][1]);
				    }
				    else
				    {
				        self::$QueryBuilder->where($value['key'],$value['value']);
				    }
				}

				if($value['type'] == "OR")
				{
					if(is_array($value['value']))
				    {
				        self::$QueryBuilder->orwhere($value['key'],$value['value'][0],$value['value'][1]);
				    }
				    else
				    {
				        self::$QueryBuilder->orwhere($value['key'],$value['value']);
				    }
				}
			}
		}
		if(self::$_orderby != "")
		{
		    self::$QueryBuilder->orderby(self::$_orderby);
		}
		
		if(self::$_limit != "")
		{
		    self::$QueryBuilder->limit(self::$_limit);
		}
		self::$where_queue = [];
		self::$_orderby = "";
		self::$_limit = "";

		$data = self::$QueryBuilder->run(1);
		if(empty($data))
			return $data;
		$modelName = get_called_class();
		$model = new $modelName();
		foreach ($data as $key => $value) {
			$model->{$key} = $value;
		}

		return $model;
	}

	public static function last_id()
	{
	    print_r(self::$QueryBuilder);
		return self::$QueryBuilder->last_id;
	}

	public static function find($id)
	{
		self::init();
		$PrimaryKey = self::getPrimaryKey();
		$data = self::$QueryBuilder->select(self::$_tbl)->where($PrimaryKey,$id)->run(1);;
		if(empty($data))
			return $data;
		$modelName = get_called_class();
		$model = new $modelName();
		foreach ($data as $key => $value) {
			$model->{$key} = $value;
		}

		return $model;
	}

	public static function where($clause1, $clause2, $clause3 = false)
	{
	   // self::init();
		self::$where_queue[] = [
			"type" => "AND",
			"key" => $clause1,
			"value" => $clause3==false ? $clause2 : [$clause2, $clause3]
		];
		
		// if(!self::$QueryBuilder->is_select)
		// 	self::$QueryBuilder->select(self::$_tbl);
		// self::$QueryBuilder->where($clause1, $clause2);
		return new static;
	}
	
	public static function orwhere($clause1, $clause2, $clause3 = false)
	{
	   // self::init();
		self::$where_queue[] = [
			"type" => "OR",
			"key" => $clause1,
			"value" => $clause3==false ? $clause2 : [$clause2, $clause3]
		];
		
		// if(!self::$QueryBuilder->is_select)
		// 	self::$QueryBuilder->select(self::$_tbl);
		// self::$QueryBuilder->where($clause1, $clause2);
		return new static;
	}
	
	public static function orderby($clause, $sort = "asc")
	{
	   // self::init();
	   
		self::$_orderby = " $clause $sort";
		
		// if(!self::$QueryBuilder->is_select)
		// 	self::$QueryBuilder->select(self::$_tbl);
		// self::$QueryBuilder->where($clause1, $clause2);
		return new static;
	}
	
	public static function limit($number_rows)
	{
	   // self::init();
	   
		self::$_limit = $number_rows;
		
		// if(!self::$QueryBuilder->is_select)
		// 	self::$QueryBuilder->select(self::$_tbl);
		// self::$QueryBuilder->where($clause1, $clause2);
		return new static;
	}

	public function hasOne($class, $criteria = array())
	{
		$model = new $class;
		if($criteria){
			foreach($criteria as $key => $value){
				$model->where($key,$this->{$value});
			}
		}
		return $model->first();
	}

	public function hasMany($class, $criteria = array())
	{
		$model = new $class;
		if($criteria){
			foreach($criteria as $key => $value){
				$model->where($key,$this->{$value});
			}
		}
		return $model->get();
	}

	public static function delete($id)
	{
		self::init();
		$PrimaryKey = self::getPrimaryKey();
		return self::$QueryBuilder->delete(self::$_tbl)->where($PrimaryKey,$id)->run();
	}

	public function save($param=false)
	{
	    self::init();
		if($param == false)
		{
			$param = [];
			foreach (self::$_fields as $key => $value) {
				if(isset($this->{$value}))
					$param[$value] = $this->{$value};
			}
		}
		$PrimaryKey = self::getPrimaryKey();
		if(isset($this->{$PrimaryKey}))
		{
			$rows = self::find($this->{$PrimaryKey});
			if(!empty($rows))
			{
				self::$QueryBuilder->is_select = 0;
				self::$QueryBuilder->is_where = 0;
				return self::$QueryBuilder->update(self::$_tbl,$param)->where($PrimaryKey,$this->{$PrimaryKey})->run();
			}
		}
		return self::$QueryBuilder->insert(self::$_tbl,$param)->run();
	}

	public static function getPrimaryKey()
	{
		self::init();
		$QueryBuilder = new QueryBuilder;
		$QueryBuilder->sql = "SHOW index FROM ".self::$_tbl." WHERE Key_name = 'PRIMARY'";
		$QueryBuilder->is_select = 1;
		$data = $QueryBuilder->run(1);
		return $data->Column_name;
	}
}