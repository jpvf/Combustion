<?php 

class Template 
{
	var $titulo  	  = '';
	var $doctype 	  = '';
	var $themes_path  = '';
	var $pos		  = array();
	
	function __construct()
	{
		$ci =& get_instance();
		
		$this->load   = $ci->load;
		$this->config = $ci->config;
		$this->uri    = $ci->uri;
		$this->parser = $ci->parser;
			
		$this->config->load('template');
		
		$this->theme = $this->config->item('theme');
		
		$this->themes_path =  APPPATH . 'themes/' . $this->theme  . '/';
		
	}
	
	function set_titulo($titulo = '')
	{
		if ($this->config->item('titulo'))
		{
			$this->titulo .= trim($this->config->item('titulo')); 
		}
		
		if ($this->config->item('separador') && $titulo != '')
		{
			$this->titulo .= ' ' . trim($this->config->item('separador')) . ' '; 
		}
		
		$this->titulo .= trim($titulo);
	}
	
	private function _get_title()
	{
		if ($this->titulo == '')
		{
			$this->set_titulo();
		}
		
		return $this->titulo;
	}
	
	function set_doctype($type = '')
	{
		if ($type == '')
		{
			$type = $this->config->item('doctype');
		}
		
		$doctype = array('xhtml11',
		 				  'xhtml1-strict',
		 				  'xhtml1-trans',
		 				  'xhtml1-frame',
		 				  'html5',
		 				  'html4-strict',
		 				  'html4-trans',
		 				  'html4-frame');
		if ( ! in_array($type, $doctype))
		{
			return FALSE;
		}
		
		$this->doctype = doctype($type);
	}
	
	private function _get_doctype()
	{
		if ($this->doctype == '')
		{
			$this->set_doctype();
		}
		
		return $this->doctype;
	}

	private function _load_theme($view, array $data, $return = FALSE)
	{
		$content = $this->load->_ci_load(array(
			'_ci_path' => $this->themes_path . $view . EXT,
			'_ci_vars' => $data,
			'_ci_return' => $return
		));
		return $content;
	}
	
	private function _position($pos = '', $view = '')
	{
		$positions = $this->config->item('position');
		$positions = ($positions) ? $positions : array();
		return $positions;
	}
	
	private function _theme_path()
	{
		$url 	= explode('/', base_url());
		$folder = $url[3]; 
		$this->theme = "/$folder/themes/{$this->theme}";
		return $this->theme;
	}
	
	function load($view = '', $data = array(), $return = FALSE)
	{
		foreach ($this->_position() as $key => $val)
		{
			$data[$key] = $this->load->view($val, $data, TRUE);
		}

		$data['theme']	 = $this->_theme_path();
		$data['doctype'] = $this->_get_doctype() . "\n";
		$data['titulo']  = $this->_get_title();
		$data['vista']   = $this->load->view($view, $data, TRUE);
		$this->_load_theme('index', $data);
	}
	
	
}