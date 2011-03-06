<?php 

class Base_model extends CI_Model
{

	function get_tables()
	{
		return generar_options($this->db->list_tables());
	}
	
	function get_campos($fuente = '')
	{
		if ($fuente == '')
		{
			return FALSE;
		}
		
		if (stripos('select', $fuente) !== FALSE)
		{
			$query = $this->db->get("$fuente");
		}
		else
		{
			$query = $this->db->query($fuente);
		}
		 
		return $query->field_data();
	}
	
	function get_elementos()
	{
		return generar_options($this->db->get('tipos_elementos')->result(), 'id', 'elemento');
	}
	
}