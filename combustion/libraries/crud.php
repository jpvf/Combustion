<?php 

class Crud 
{
	var $table = '';
	var $fields = array();
	var $id = array();
	
	function __construct()
	{
		$this->ci =& get_instance();
		$this->db = $this->ci->db;
		$this->load = $this->ci->load;
	}
	
	private function _set_db_table($table = '')
	{
		if ( ! $this->db->table_exists($table))
		{
			echo 'La tabla especificada no existe';
			die();
		}
		$this->table = $table;
	}
	
	private function _set_fields()
	{
		$fields = $this->db->field_data($this->table);
		
		foreach ($fields as $field)
		{
			if ($field->primary_key == 1)
			{
				$this->id = $field; 
			}
			elseif ($field->name != 'activo')
			{
				$this->fields[] = $field; 
			}
		}
	}
	
	private function _view()
	{
		$this->model = <<<MODEL
<?php 

class {$this->table}_model extends CI_Model
{

	function get_all(\$per_page = NULL, \$offset = NULL, \$by = NULL, \$order = NULL)
	{
		\$order = (\$order == 'desc') ? 'desc' : 'asc';
		\$columnas = array('nombre', 'descripcion', 'version', 'creado');
		\$by = (in_array(\$by, \$columnas)) ? \$by : 'id';
		
		\$this->db->order_by(\$by, \$order);
		\$this->db->limit(\$per_page, \$offset);	
		\$this->db->where("activo = 1");
		return \$this->db->get("{$this->table}");
	}


MODEL;

		$table_head = array();
		$rows		= array();
		
		foreach ($this->fields as $field)
		{
			$name = ucwords(str_replace('_', ' ', $field->name));
			$table_head[] = "anchor('{$this->table}/index/{$field->name}/' .
							((\$order == 'asc' && \$by == '{$field->name}') ? 'desc' : 'asc'). 
							((\$offset > 0) ? \"/\$offset\" : '') ,
							\"$name <span class='ui-icon ui-icon-triangle-1-\" .
							((\$order == 'asc' && \$by == '{$field->name}') ? 'n' : 's') . 
							\"'></span>\", array('class' => 'table-order'))";
			$rows[]	  = $field->name;
		}

		$cols = count($rows) + 1;
		
		$table_head = implode(", \n ", $table_head);
		$rows 		= "\$row->" . implode(", \$row->", $rows);
		
		$this->controller = <<<CONTROLLER
<?php

class {$this->table} extends MY_Controller
{

	function index(\$by = 'default', \$order = 'asc', \$offset = 0)
	{
		\$this->load->model('{$this->table}_model');
		\$this->load->library('table');
		
		\$results = \$this->{$this->table}_model->get_all();	

		\$this->load->library('pagination');

		\$config['base_url']   = base_url() . "{$this->table}/index/\$by/\$order/";
		\$config['total_rows'] = \$results->num_rows();
		\$config['per_page']   = '10'; 
		\$config['uri_segment']= 5; 
		\$config['full_tag_open'] = '<ul class="pagination">';
		\$config['full_tag_close'] = '</ul>';
		\$config['cur_tag_open'] = '<li class="active">';
		\$config['cur_tag_close'] = '</li>';
		\$config['num_tag_open'] = '<li>';
		\$config['num_tag_close'] = '</li>';
		\$config['next_link'] = '&gt;';
		\$config['next_tag_open'] = '<li>';
		\$config['next_tag_close'] = '</li>';
		\$config['prev_link'] = '&lt;';
		\$config['prev_tag_open'] = '<li>';
		\$config['prev_tag_close'] = '</li>';
		
		\$this->pagination->initialize(\$config); 	
		
		\$tmpl = array ( 'table_open'  => '<table class="tables table-full ui-widget">',
						 'heading_cell_start'  => '<th class="ui-state-default">',
                    	 'heading_cell_end'    => '</th>', );

		\$this->table->set_template(\$tmpl);
		
		\$this->table->set_heading($table_head, '&nbsp');
		
		\$results = \$this->{$this->table}_model->get_all(\$config['per_page'], \$offset, \$by, \$order);	

		if (\$results->num_rows() > 0)
		{
			foreach (\$results->result() as \$row)
			{
				\$this->table->add_row($rows, 
										anchor("{$this->table}/detalles/\$row->{$this->id->name}",'Detalles') 
										. ' | ' . 
										anchor("{$this->table}/editar/\$row->{$this->id->name}",'Editar')
										. ' | ' .
										anchor("{$this->table}/eliminar/\$row->{$this->id->name}",'Eliminar')
										);
			}
		}
		else
		{
			\$cell = array('data' => 'No hay registros', 'colspan' => $cols);
			\$this->table->add_row(\$cell);	
		}
		
		
		\$data['resultados'] = \$this->table->generate();
		\$data['paginacion'] = \$this->pagination->create_links();
		\$this->template->set_titulo('{$this->table}');
		\$this->template->load('ver', \$data);
	}

	
CONTROLLER;

		$this->view_view = <<<VIEW

	<div class='box full'>
		<h4>Proyectos</h4>
		<div class='box-content'>
			<?php echo anchor('{$this->table}/nuevo', 'Nuevo', array('class' => 'button')); ?>
			<?php echo br(3); ?>
			<?php echo \$resultados;?>	
			<?php echo \$paginacion; ?>
		</div>
	</div>
VIEW;
		
		
	}	
	
	private function _crear_directorio($dir = '')
	{
		echo "Creando ... $dir <br>"; 
		if ( ! is_dir($dir))
		{
			mkdir($dir, 0700);
		}
		else
		{
			echo "Ya existe el directorio ... $dir <br>";
		}
	}
	
	private function _crear_archivo($archivo = '', $contenido = '')
	{
		echo "Creando ... $archivo <br>";
		$ourFileName = $archivo;
		$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
		fwrite($ourFileHandle, $contenido);
		fclose($ourFileHandle);
	}
	
	private function _add()
	{
		$this->load->library('forms');		
		$lista = $this->ci->forms->generate($this->fields, FALSE, "\t\t<?php echo anchor('{$this->table}/index', 'Cancelar', array('class' => 'button align-right')); ?>");
		$this->model .= <<<MODEL

	function save(\$data = array(), \$id = 0)
	{
		if (count(\$data) == 0)
		{
			return FALSE;
		}
		
		if (\$id == 0)
		{
			return \$this->db->insert("{$this->table}", \$data);	
		}
		\$this->db->where('id', \$id);
		return \$this->db->update("{$this->table}", \$data);
	}


MODEL;

		$table_head = array();
				
		foreach ($this->fields as $field)
		{
			if ($field->name != 'creado')
			{
				$rows[]	  = "'{$field->name}' => \$this->input->post('{$field->name}')";
			}
			else 
			{
				$rows[]	  = "'{$field->name}' => today(TRUE)";
				
			}
		}
		

		$rows = implode(", \n", $rows);
		
		
		$this->controller .= <<<CONTROLLER
	function nuevo()
	{
		if (\$_POST)
		{
			\$this->_guardar();
			exit;
		}		
		\$this->template->set_titulo('{$this->table}');
		\$this->template->load('nuevo');
	}
	
	private function _guardar()
	{
		\$this->load->model('{$this->table}_model');
		\$insert = array($rows);
		\$this->{$this->table}_model->save(\$insert);
		
		if (\$this->db->affected_rows() > 0)
		{
			mensaje_ok('Proyecto Guardado', 'proyectos/index');
		}

		mensaje_error('Problema guardando','proyectos/index');		
	}

CONTROLLER;

		$this->add_view = <<<VIEW

	<div class='box full'>
		<h4>Proyectos</h4>
		<div class='box-content'>
			<?php echo form_open('{$this->table}/nuevo', array('class' => 'forms', 'id' => 'nuevo-{$this->table}'));?>
				$lista
			<?php echo form_close(); ?>	
		</div>
	</div>	
VIEW;
	}
	
	private function _details()
	{
		$this->model .= <<<MODEL

	function details(\$id = 0)
	{
		\$this->db->where(array('id' => \$id, 'activo' => 1));
		return \$this->db->get("{$this->table}");
	}


MODEL;
		$table_head = array();
				
		foreach ($this->fields as $field)
		{
			$label  = ucwords(str_replace('_', ' ',$field->name));
			$rows[]	= " \"<label for='{$field->name}'> $label </label><span>\" . \$row->{$field->name} . \"</span>\" ";
		}
		

		$rows = implode(", \n", $rows);
		
		
		
		$this->controller .= <<<CONTROLLER
	function detalles(\$id = 0)
	{
		if ( ! \$id)
		{
			redirect('{$this->table}/index');
		}
		
		\$this->load->model('{$this->table}_model');
		
		\$row = \$this->{$this->table}_model->details(\$id)->row();	
			
		\$data['details'] = ul(array($rows), array('class' => 'list'));
		\$data['id']      = \$id;
		\$this->template->set_titulo('{$this->table}');
		\$this->template->load('detalles', \$data);
	}
CONTROLLER;

		$this->details_view = <<<VIEW

	<div class='box full'>
		<h4>Proyectos</h4>
		<div class='box-content'>
			<?php echo \$details; ?>
			<?php echo br(3); ?>
			<?php echo anchor('{$this->table}/index', 'Volver', array('class' => 'button')); ?>
			<?php echo anchor("{$this->table}/editar/\$id", 'Editar', array('class' => 'button')); ?>	
			<?php echo anchor("{$this->table}/eliminar/\$id", 'Eliminar', array('class' => 'button')); ?>		
		</div>
	</div>	
VIEW;
	}
	
	private function _edit()
	{
		$this->load->library('forms');		
		$lista = $this->ci->forms->generate($this->fields, TRUE , "\t\t<?php echo anchor('{$this->table}/index', 'Cancelar', array('class' => 'button align-right')); ?>");
		
		foreach ($this->fields as $field)
		{
			if ($field->name != 'creado')
			{
				$rows[]	  = "'{$field->name}' => \$this->input->post('{$field->name}')";
			}
		}
		

		$rows = implode(", \n", $rows);
		
		$this->controller .= <<<CONTROLLER
	function editar(\$id = 0)
	{
		if ( ! \$id AND ! \$_POST)
		{
			redirect('{$this->table}/index');
		}
		
		if (\$_POST)
		{
			\$this->_actualizar();
			exit;
		}
		
		\$this->load->model('{$this->table}_model');
		\$data['id']  = \$id;
		\$data['row'] = \$this->{$this->table}_model->details(\$id)->row();	
			
		\$this->template->set_titulo('{$this->table}');
		\$this->template->load('editar', \$data);
	}
	
	private function _actualizar()
	{
		\$this->load->model('{$this->table}_model');
		\$update = array($rows);
		\$this->{$this->table}_model->save(\$update, \$this->input->post('id'));
		
		if (\$this->db->affected_rows() > 0)
		{
			mensaje_ok('Proyecto Guardado', 'proyectos/index');
		}

		mensaje_error('Problema guardando','proyectos/index');		
	}
CONTROLLER;

		$this->edit_view = <<<VIEW

	<div class='box full'>
		<h4>Proyectos</h4>
		<div class='box-content'>
			<?php echo form_open('{$this->table}/editar', array('class' => 'forms', 'id' => 'nuevo-{$this->table}'));?>
				$lista
				<?php echo form_hidden('id', \$id); ?>
			<?php echo form_close(); ?>	
			<?php echo br(3); ?>
			<?php echo anchor('{$this->table}/index', 'Volver', array('class' => 'button')); ?>
			<?php echo anchor("{$this->table}/detalles/\$id", 'Ver', array('class' => 'button')); ?>
			<?php echo anchor("{$this->table}/eliminar/\$id", 'Eliminar', array('class' => 'button')); ?>			
		</div>
	</div>	
VIEW;
	}
	
	
	private function _delete()
	{	

		$this->controller .= <<<CONTROLLER
	function eliminar(\$id = 0)
	{
		if ( ! \$id)
		{
			redirect('{$this->table}/index');
		}
			
		\$this->load->model('{$this->table}_model');
		\$update = array('activo' => 0);
		\$this->{$this->table}_model->save(\$update, \$id);
		
		if (\$this->db->affected_rows() > 0)
		{
			mensaje_ok('Proyecto Guardado', 'proyectos/index');
		}

		mensaje_error('Problema guardando','proyectos/index');		
	}
CONTROLLER;

	}
	function generate($table = '', $query = '')
	{
		if ($table == '')
		{
			echo 'Debe especificar una tabla';
			die();
		}
		
		$this->_set_db_table($table);
		$this->_set_fields();
		
		$this->_view();
		$this->_add();
		$this->_details();
		$this->_edit();
		$this->_delete();
		
		$output = APPPATH .'output/' . $this->table;
		
		$this->_crear_directorio($output);
		$this->_crear_directorio($output . '/controllers');
		$this->_crear_directorio($output . '/models');
		$this->_crear_directorio($output . '/views');
					
		$this->_crear_archivo($output . '/controllers/' . $this->table . EXT, $this->controller . '}');
		$this->_crear_archivo($output . '/models/' . $this->table . '_model' . EXT, $this->model . '}');
		$this->_crear_archivo($output . '/views/ver' . EXT, $this->view_view);
		$this->_crear_archivo($output . '/views/nuevo' . EXT, $this->add_view);
		$this->_crear_archivo($output . '/views/detalles' . EXT, $this->details_view);
		$this->_crear_archivo($output . '/views/editar' . EXT, $this->edit_view);
		die();
	}	
	
}