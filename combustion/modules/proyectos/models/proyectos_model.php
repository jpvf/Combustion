<?php 

class proyectos_model extends CI_Model
{

	function get_all($per_page = NULL, $offset = NULL, $by = NULL, $order = NULL)
	{
		$order = ($order == 'desc') ? 'desc' : 'asc';
		$columnas = array('nombre', 'descripcion', 'version', 'creado');
		$by = (in_array($by, $columnas)) ? $by : 'id';
		
		$this->db->order_by($by, $order);
		$this->db->limit($per_page, $offset);	
		$this->db->where("activo = 1");
		return $this->db->get("proyectos");
	}


	function save($data = array(), $id = 0)
	{
		if (count($data) == 0)
		{
			return FALSE;
		}
		
		if ($id == 0)
		{
			return $this->db->insert("proyectos", $data);	
		}
		$this->db->where('id', $id);
		return $this->db->update("proyectos", $data);
	}


	function details($id = 0)
	{
		$this->db->where(array('id' => $id, 'activo' => 1));
		return $this->db->get("proyectos");
	}

}