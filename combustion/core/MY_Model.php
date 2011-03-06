<?php

class MY_model extends CI_Model {
	
	## All database fields fall in to "attributes"
	var $attributes				= FALSE;
	
	## Relationships
	var $has_many				= FALSE;
	var $has_one				= FALSE;
	var $belongs_to				= FALSE;
	
	## Primary key value
	var $primary_key			= "id";
	
	## Hooks before/after update/save
	var $before_update			= array();
	var $after_update			= array();
	var $before_save			= array();
	var $after_save				= array();
	
	## Private class vars
	private $_table_name		= '';
	private $_table_prefix		= '';
	private $_error_message		= FALSE;
	private $_load_time			= 0;
	
	function __construct($id=FALSE, $table=FALSE) 
	{
		$time_start = microtime(true);
		
		## Setup the table variable
		if ($table) {
			if ($this->table($table)) {
				## Did they pass in an id?
				if ($id) {
					$this->_setup($id);
				}
			}
		}
		
		$time_end = microtime(true);
		$this->_load_time = $time_end - $time_start;
	}
	
	function __get($var) 
	{
		## Check to see if var exists as relationship - Lazy Loading FTW
		$class = "";
		$type = "";
		
		$relationships = array("has_one", "has_many", "belongs_to");
		foreach ($relationships as $relationship) {
			if ($this->$relationship) {
				foreach ($this->$relationship as $r) {
					$data = array();
					if (is_array($r)) {
						$data = $r;
					} else { 
						$data["class"] = $r;
					}

					if ($var == $data["class"]) {
						$data["type"] = $relationship;
						$this->spawn($data);
						return $this->$data["class"];
					}
				}
			}
		}
		
		## Check to see if var exists as attribute
		if (isset($this->attributes->$var)) {
			return $this->attributes->$var;
		}
	}
	
	function __set($var,$value)
	{
		if (!is_object($this->attributes)) {
			$this->attributes["id"] = FALSE;
			$this->attributes[$var] = $value;
		} else {
			$this->attributes->$var = $value;
		}
		return TRUE;
	}
	
	function __call($name,$arg) 
	{
		if (!function_exists($this->$name)) {
			
			## Break it down and see if it's trying to do a "find_by"
			if (strpos($name, "find_by_") == 0) {
				
				## The user is trying to do a find by
				$field = str_replace("find_by_","",$name);
				
				if ($this->table()) {
					$CI =& get_instance();
					$query = $CI->db->get_where($this->_table_name, array($field => $arg[0]));
					
					if ($query->num_rows() > 0) {
						$row = $query->row();
						$pk = $this->primary_key;
						$this->_setup($row->$pk);
						return $this;
					}
				} else {
					$this->error("Your object is not setup. Please set a table using the table() function.");
					return FALSE;
				}
				
			}
			
		}
	}
	
	function _before_update()
	{
		foreach ($this->before_update as $function) {
			$this->$function();
		}
	}
	
	function _after_update()
	{
		foreach ($this->after_update as $function) {
			$this->$function();
		}
	}
	
	function _before_save()
	{
		foreach ($this->before_save as $function) {
			$this->$function();
		}
	}
	
	function _after_save()
	{
		foreach ($this->after_save as $function) {
			$this->$function();
		}
	}
	
	function _setup($id) 
	{
		if ($this->table()) {
			$CI =& get_instance();
			
			## Determine the table prefix
			$prefix = explode("_", $this->_table_name);
			if (count($prefix) > 1) { 
				$this->_table_prefix = $prefix[0]."_";
			} 
			
			$query = $CI->db->get_where($this->table(), array($this->primary_key => $id));
			if ($query->num_rows() > 0) {
				
				## Set the attributes in the object
				$this->attributes = $query->row();
				
				return TRUE;
			} else {
				$this->error("A record can not be found using ID:$id.");
				return FALSE;
			}
		} else {
			$this->error("Your object is not setup. Please set a table using the table() function.");
			return FALSE;
		}
	}
	
	function table($val=FALSE) 
	{
		if ($val) {
			$CI =& get_instance();
			if ($CI->db->table_exists($val)) {
				return $this->_table_name = $val;
			}
		} else {
			return $this->_table_name;
		}
	}
	
	function error($val=FALSE) 
	{
		if ($val) {
			return $this->_error_message .= ' '.$val;
		} else {
			return $this->_error_message;
		}
	}
	
	function refresh($id=FALSE)
	{
		if ($id) {
			return $this->_setup($id);
		} else {
			return $this->_setup($this->attributes->id);
		}
	}
	
	function create($params)
	{
		if ($this->table()) {
			
			$CI =& get_instance();
			
			## Grab a local list of all the fields
			$fields = $CI->db->get_where("information_schema.COLUMNS", array("TABLE_SCHEMA" => $CI->db->database, "TABLE_NAME" => $this->_table_name));
			$field_names = array();
						
			## Find the fields that are "required" per the database
			$required_fields = array();
			foreach($fields->result() as $field) {
				$field_names[] = $field->COLUMN_NAME;
				if ($field->IS_NULLABLE == "NO" && $field->COLUMN_NAME != $this->primary_key && is_null($field->COLUMN_DEFAULT)) {
					$required_fields[] = $field->COLUMN_NAME;
				}
			}
			
			## Check for all required fields on input
			foreach($required_fields as $field) {
				if (empty($params[$field]) === true) {
					error_log('Field "'.$field.'" is required to create a row on table "'.$this->_table_name.'"');
					return FALSE;
				}
			}
			
			## Check for fields that don't exist
			foreach($params as $key => $value) {
				if (!in_array($key, $field_names)) {
					unset($params[$key]);
				}
			}
			
			## Set default fields
			$params["created_at"] = date("Y-m-d h:i:s");
			$params["updated_at"] = date("Y-m-d h:i:s");
			
			$this->_before_save();
			if ($CI->db->insert($this->_table_name,$params)) {
				$return_id = $CI->db->insert_id();
				$this->_after_save();
				$this->refresh($return_id);
				return $return_id;
			} else {
				error_log('Your insert query has an error. '.$CI->db->_error_message());
			}
			
		} else {
			$this->error("Your object is not setup. Please set a table using the table() function.");
			return FALSE;
		}
		
		return FALSE;
	}
	
	function update_attribute($field,$value)
	{
		$this->_before_update();
		
		if ($this->table()) {
			
			$CI =& get_instance();
			
			$this->_before_save();
			$CI->db->update($this->_table_name, array($field => $value), array("id" => $this->attributes->id));
			$this->_after_save();
			
			if ($CI->db->_error_message() == '') { 
				$this->_after_update();			
				$this->refresh();
				return TRUE; 
			} else {
				$this->_error_message = $CI->db->_error_message();
				return FALSE;
			}
			
		}
		
		return FALSE;
	}
	
	function update_attributes($fields)
	{
		foreach ($fields as $name => $value) {
			if (!$this->update_attribute($name, $value)) {
				return FALSE;
			}
		}
		return TRUE;
	}
	
	function destroy()
	{
		if ($this->table()) {
			
			$CI =& get_instance();
			
				if ($return = $CI->db->delete($this->_table_name, array('id' => $this->attributes->id))) {
				$this->attributes = FALSE;
				return TRUE;
			}
			
		}
		return FALSE;
	}
	
	function all($filter=FALSE,$order="")
	{
		if ($this->table()) {
			$CI =& get_instance();
			
			if ($order != "") {
				$CI->db->order_by($order);
			}
			
			if ($filter) {
				return ($CI->db->get_where($this->_table_name, $filter));
			} else {
				return ($CI->db->get($this->_table_name));	
			}
		} else {
			$this->error("Your object is not setup. Please set a table using the table() function.");
			return FALSE;
		}
	}
	
	function spawn($params)
	{
		$CI =& get_instance();
		
		## Set Class Variable
		if (isset($params["class"])) { $class = strtolower(singular($params["class"])); } else { return FALSE; }
		
		## Set Type Variable
		if (isset($params["type"])) { $type = $params["type"]; } else { return FALSE; }
		
		## Load up an instance of the class
		$CI->load->model($class);
		$obj = new $class;
		
		## Proceed based on "type"
		switch($type) {
			
			case "has_many":
				
				## Check for specific foreign key
				if (isset($params["foreign_key"])) { $foreign_key = $params["foreign_key"]; } else { $foreign_key = $this->get_foreign_key(); }
				
				## Setup the children
				$obj_array = array();
				$tmp_attr = (array) $this->attributes;
				$children = $obj->all(array($foreign_key => $tmp_attr[$this->primary_key]));
				
				## Push children in to array of objects
				foreach($children->result() as $child) {
					$obj_class = new $class;
					$obj_class->_setup($child->id);
					$obj_array[] = $obj_class;
				}

				## Pluralize the class and create an array
				$var_name = plural($class);
				$this->$var_name = $obj_array;
			
				break;
				
			case "belongs_to":
			case "has_one":
				
				if ($type == "belongs_to") {
					
					## Check for specific foreign key
					if (isset($params["foreign_key"])) { $foreign_key = $params["foreign_key"]; } else { $foreign_key = $class."_id"; }
					
				}
				
				if ($type == "has_one") {
					
					## Check for specific foreign key
					if (isset($params["foreign_key"])) { $foreign_key = $params["foreign_key"]; } else { $foreign_key = $this->get_foreign_key(); }
					
				}
				
				## Setup the parent record
				$obj->_setup($this->$foreign_key);
				
				## Save it in to the object
				$this->$class = $obj;
			
				break;
				
			default:
				return FALSE;
				
		}
		
		return TRUE;
	}

	function save()
	{
		if ($this->attributes) {
			if (is_object($this->attributes)) {
				## Update
				return $this->update_attributes((array) $this->attributes);
			} else {
				## Create
				return $this->create($this->attributes);
			}
		}
		return FALSE;
	}
	
	function is_new()
	{
		if (!$this->attributes || !is_object($this->attributes)) {
			return TRUE;
		}
	}
	
	##
	## Private Functions
	##
	
	private function get_foreign_key()
	{
		$key = singular(str_replace($this->_table_prefix, '', $this->_table_name))."_id";
		return $key;
	}

}