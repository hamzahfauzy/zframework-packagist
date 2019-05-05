<?php
namespace vendor\zframework;

class Migration 
{
	private $file_content;

	public function __construct($file_content)
	{
		$this->file_content = $this->prepareJSON($file_content);
	}

	function prepareJSON($input) {

	    //This will convert ASCII/ISO-8859-1 to UTF-8.
	    //Be careful with the third parameter (encoding detect list), because
	    //if set wrong, some input encodings will get garbled (including UTF-8!)
	    $imput = mb_convert_encoding($input, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');

	    //Remove UTF-8 BOM if present, json_decode() does not like it.
	    if(substr($input, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) $input = substr($input, 3);

	    return $input;
	}

	public function parseToQuery()
	{
		$jsonData = json_decode($this->file_content);
		$sql = "";

		if($jsonData->action == "create")
		{
			$sql = "CREATE TABLE ".$jsonData->table_name;
		}

		if($jsonData->action == "add column")
		{
			$sql = "ALTER TABLE ".$jsonData->table_name;
		}

		$fields = "";
		foreach ($jsonData->fields as $key => $value) {
			$fields .= " ".$value->name." ".$value->data_type;
			if(isset($value->length))
			{
				$fields .= "(".$value->length.")";
			}
			if(isset($value->default))
			{
				$fields .= " ".$value->default;
			}
			else
			{
				$fields .= " NOT NULL";
			}
			if (next($jsonData->fields)==true) $fields .= ",";
		}

		if(isset($jsonData->primary_key))
		{
			$fields .= ", PRIMARY KEY (".$jsonData->primary_key.")";
		}

		if(isset($jsonData->foreign_key))
		{
			foreach ($jsonData->foreign_key as $key => $value) {
				$fields .= ", FOREIGN KEY (".$value->field_name.")";
				$fields .= " REFERENCES ".$value->references."(".$value->references_field.")";
				$fields .= " ON DELETE ".$value->event->delete;
				$fields .= " ON UPDATE ".$value->event->update;
			}
		}

		$sql .= "(".$fields.")";

		return $sql;
	}
}