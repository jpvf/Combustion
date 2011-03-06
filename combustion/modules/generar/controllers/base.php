<?php 

class Base extends MY_Controller
{
	function __construct()
	{
		parent::__construct();		
		$this->load->model('generar/base_model');
	}	

	function index()
	{
		$config['hostname'] = "localhost";
		$config['username'] = "root";
		$config['password'] = "";
		$config['database'] = "pyrocms";
		$config['dbdriver'] = "mysql";
		$config['dbprefix'] = "";
		$config['pconnect'] = FALSE;
		$config['db_debug'] = TRUE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = "";
		$config['char_set'] = "utf8";
		$config['dbcollat'] = "utf8_general_ci";
		
		$db = $this->load->database($config, TRUE);
		/*
		echo 'generador';		
		$results = $db->get('users');
		debug($results->result());
		
		$results = $this->db->get('proyectos');
		debug($results->result());
		//debug($db);*/
		$data['tablas'] = $this->base_model->get_tables();
		$this->view('base/index', $data);		
	}
	
	function primer_paso()
	{
		$tabla 	  = $this->input->post('tabla');
		$consulta = $this->input->post('consulta');
		
		if ( ! $consulta)
		{
			$fields = $this->base_model->get_campos($tabla);
		}
		else
		{
			$fields = $this->base_model->get_campos($consulta);
		}
		
		$this->load->library('forms');
		
		debug($this->forms->generate($fields));
		
		
		die();

		$data['elementos']   			= $this->base_model->get_elementos();
		$data['elementos']['fecha_hoy'] = 'fecha de hoy';
		$data['campos'] 				= $fields;
		$this->view('generar/base/primer_paso', $data);
	}
	
	function segundo_paso()
	{
		debug($_POST);
	}
	
	
	
	


}