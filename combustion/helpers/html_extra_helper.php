<?php 

if ( ! function_exists('tabs'))
{
	/**
	*
	* Estructura del array tabs
	* array('href', 'Titulo|segmento');
	* array('/operacion/index', 'Operaciones|index')
	*
	* El segmento es el que va a indicar si marcado
	* como activo o no.
	*/

	function tabs($tabs = array(), $segmento = 0)
	{
		$li = array();
		$CI =& get_instance();

		foreach ($tabs  as $key => $val)
		{
			$seg = '';

			if (strpos($val, '|') !== FALSE)
			{
				list($val, $seg) = explode('|', $val);					
			}

			$clase = '';

			if ($seg == $CI->uri->segment($segmento))
			{
				$clase = ' class="selected"';
			}

			$li[] = "<li$clase>" . anchor($key, $val) . '</li>';
		}

		$ul = '<ul class="tabs">' . implode('', $li) . '</ul>';

		return $ul;
	}

}


if ( ! function_exists('generar_options'))
{
	
	function generar_options($rows = array(), $id = '', $nombre = '')
	{
		if (count($rows) == 0)
		{
			return $rows;
		}
		
		$options[] = 'Seleccione';
		
		if ($id == '')
		{		
			foreach ($rows as $row)
			{
				$options[$row] = $row;
			}
		}
		else
		{
			foreach ($rows as $row)
			{
				$options[$row->$id] = $row->$nombre;
			}
		}
		return $options;
	}

}