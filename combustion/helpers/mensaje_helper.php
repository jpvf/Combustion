<?php 

if ( ! function_exists('mensaje_ok'))
{
	
	function mensaje_ok($mensaje = '', $url = '')
	{
		
		mensaje($mensaje, 'success', $url);

	}

}

if ( ! function_exists('mensaje_error'))
{
	
	function mensaje_error($mensaje = '', $url = '')
	{
		
		mensaje($mensaje, 'error', $url);

	}

}

if ( ! function_exists('mensaje_info'))
{
	
	function mensaje_info($mensaje = '', $url = '')
	{
		
		mensaje($mensaje, 'info', $url);

	}

}

if ( ! function_exists('mensaje_alert'))
{
	
	function mensaje_alert($mensaje = '', $url = '')
	{
		
		mensaje($mensaje, 'alert', $url);

	}

}

if ( ! function_exists('mensaje'))
{
	
	function mensaje($mensaje = '', $tipo = '', $url = '')
	{
		
		$CI =& get_instance();
		$CI->session->set_flashdata('mensaje', array('tipo' => $tipo , 'mensaje' => $mensaje));	
		redirect($url);
	}

}