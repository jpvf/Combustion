<?php 

if ( ! file_exists('today'))
{
	
	function today($hour = FALSE)
	{
		if ($hour != FALSE)
		{
			$date = date("Y-m-d H:i:s");
		}
		else
		{
			$date = date('Y-m-d');
		}		

		return $date;
	}

}