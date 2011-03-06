<?php

class Test extends MY_Controller
{
	function index()
	{
		$this->load->library('template');
		$this->load->library('crud');
		$this->crud->generate('proyectos');
		$this->template->set_titulo('Test');
		$this->template->load('inicio/inicio_index');
	}
	
}