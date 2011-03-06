<?php

if ( ! function_exists('debug'))
{
	
	function debug($line = array(), $die = FALSE)
	{
		echo '<pre>';
		print_r($line);
		echo '</pre>';
		
		if ($die === TRUE)
		{
			die();
		}
		
	}

}