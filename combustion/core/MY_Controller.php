<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* The MX_Controller class is autoloaded as required */

class MY_Controller extends CI_Controller
{
	var $titulo = '';

	function __construct()
	{
		parent::__construct();		
	}
	
	function view($view = '', $data = array())
	{
		$data['titulo'] = $this->titulo();
		$data['vista']  = $view;
		$this->load->view('template', $data);
	}

	function titulo($titulo = 'Combustion v0.1')
	{
		$this->titulo = $titulo;	
		return $this->titulo;	
	}


}