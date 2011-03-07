<?php

class proyectos extends MY_Controller
{

	function index($by = 'default', $order = 'asc', $offset = 0)
	{
		$this->load->model('proyectos_model');
		$this->load->library('table');
		
		$results = $this->proyectos_model->get_all();	

		$this->load->library('pagination');

		$config['base_url']   = base_url() . "proyectos/index/$by/$order/";
		$config['total_rows'] = $results->num_rows();
		$config['per_page']   = '10'; 
		$config['uri_segment']= 5; 
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['next_link'] = '&gt;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_link'] = '&lt;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		$this->pagination->initialize($config); 	
		
		$tmpl = array ( 'table_open'  => '<table class="tables table-full ui-widget">',
						 'heading_cell_start'  => '<th class="ui-state-default">',
                    	 'heading_cell_end'    => '</th>', );

		$this->table->set_template($tmpl);
		
		$this->table->set_heading(anchor('proyectos/index/nombre/' .
							(($order == 'asc' && $by == 'nombre') ? 'desc' : 'asc'). 
							(($offset > 0) ? "/$offset" : '') ,
							"Nombre <span class='ui-icon ui-icon-triangle-1-" .
							(($order == 'asc' && $by == 'nombre') ? 'n' : 's') . 
							"'></span>", array('class' => 'table-order')), 
 anchor('proyectos/index/descripcion/' .
							(($order == 'asc' && $by == 'descripcion') ? 'desc' : 'asc'). 
							(($offset > 0) ? "/$offset" : '') ,
							"Descripcion <span class='ui-icon ui-icon-triangle-1-" .
							(($order == 'asc' && $by == 'descripcion') ? 'n' : 's') . 
							"'></span>", array('class' => 'table-order')), 
 anchor('proyectos/index/version/' .
							(($order == 'asc' && $by == 'version') ? 'desc' : 'asc'). 
							(($offset > 0) ? "/$offset" : '') ,
							"Version <span class='ui-icon ui-icon-triangle-1-" .
							(($order == 'asc' && $by == 'version') ? 'n' : 's') . 
							"'></span>", array('class' => 'table-order')), 
 anchor('proyectos/index/creado/' .
							(($order == 'asc' && $by == 'creado') ? 'desc' : 'asc'). 
							(($offset > 0) ? "/$offset" : '') ,
							"Creado <span class='ui-icon ui-icon-triangle-1-" .
							(($order == 'asc' && $by == 'creado') ? 'n' : 's') . 
							"'></span>", array('class' => 'table-order')), '&nbsp');
		
		$results = $this->proyectos_model->get_all($config['per_page'], $offset, $by, $order);	

		if ($results->num_rows() > 0)
		{
			foreach ($results->result() as $row)
			{
				$this->table->add_row($row->nombre, $row->descripcion, $row->version, $row->creado, 
										anchor("proyectos/detalles/$row->id",'Detalles') 
										. ' | ' . 
										anchor("proyectos/editar/$row->id",'Editar')
										. ' | ' .
										anchor("proyectos/eliminar/$row->id",'Eliminar')
										);
			}
		}
		else
		{
			$cell = array('data' => 'No hay registros', 'colspan' => 5);
			$this->table->add_row($cell);	
		}
		
		
		$data['resultados'] = $this->table->generate();
		$data['paginacion'] = $this->pagination->create_links();
		$this->template->set_titulo('proyectos');
		$this->template->load('ver', $data);
	}

		function nuevo()
	{
		if ($_POST)
		{
			$this->_guardar();
			exit;
		}		
		$this->template->set_titulo('proyectos');
		$this->template->load('nuevo');
	}
	
	private function _guardar()
	{
		$this->load->model('proyectos_model');
		$insert = array('nombre' => $this->input->post('nombre'), 
'descripcion' => $this->input->post('descripcion'), 
'version' => $this->input->post('version'), 
'creado' => today(TRUE));
		$this->proyectos_model->save($insert);
		
		if ($this->db->affected_rows() > 0)
		{
			mensaje_ok('Proyecto Guardado', 'proyectos/index');
		}

		mensaje_error('Problema guardando','proyectos/index');		
	}
	function detalles($id = 0)
	{
		if ( ! $id)
		{
			redirect('proyectos/index');
		}
		
		$this->load->model('proyectos_model');
		
		$row = $this->proyectos_model->details($id)->row();	
			
		$data['details'] = ul(array( "<label for='nombre'> Nombre </label><span>" . $row->nombre . "</span>" , 
 "<label for='descripcion'> Descripcion </label><span>" . $row->descripcion . "</span>" , 
 "<label for='version'> Version </label><span>" . $row->version . "</span>" , 
 "<label for='creado'> Creado </label><span>" . $row->creado . "</span>" ), array('class' => 'list'));
		$data['id']      = $id;
		$this->template->set_titulo('proyectos');
		$this->template->load('detalles', $data);
	}	function editar($id = 0)
	{
		if ( ! $id AND ! $_POST)
		{
			redirect('proyectos/index');
		}
		
		if ($_POST)
		{
			$this->_actualizar();
			exit;
		}
		
		$this->load->model('proyectos_model');
		$data['id']  = $id;
		$data['row'] = $this->proyectos_model->details($id)->row();	
			
		$this->template->set_titulo('proyectos');
		$this->template->load('editar', $data);
	}
	
	private function _actualizar()
	{
		$this->load->model('proyectos_model');
		$update = array('nombre' => $this->input->post('nombre'), 
'descripcion' => $this->input->post('descripcion'), 
'version' => $this->input->post('version'));
		$this->proyectos_model->save($update, $this->input->post('id'));
		
		if ($this->db->affected_rows() > 0)
		{
			mensaje_ok('Proyecto Guardado', 'proyectos/index');
		}

		mensaje_error('Problema guardando','proyectos/index');		
	}	function eliminar($id = 0)
	{
		if ( ! $id)
		{
			redirect('proyectos/index');
		}
			
		$this->load->model('proyectos_model');
		$update = array('activo' => 0);
		$this->proyectos_model->save($update, $id);
		
		if ($this->db->affected_rows() > 0)
		{
			mensaje_ok('Proyecto Guardado', 'proyectos/index');
		}

		mensaje_error('Problema guardando','proyectos/index');		
	}}