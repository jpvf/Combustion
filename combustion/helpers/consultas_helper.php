<?php


if ( ! function_exists('format_query'))
{
	
	function format_query($sql = '')
	{
		if ($sql == '')
		{
			return FALSE;			
		}

		$sql = explode('<br />', nl2br($sql));

		$sql = str_ireplace( 
                            array ( ';',
                                    'select ', 
                                    'UPDATE ', 
                                    'DELETE ', 
                                    'INSERT ', 
                                    'INTO ', 
                                    'VALUES ', 
                                    'FROM ', 
                                    'LEFT ', 
                                    'RIGHT ',
                                    'INNER ',
                                    'JOIN ', 
                                    'WHERE ', 
                                    'LIMIT ', 
                                    'ORDER BY ', 
                                    'AND ', 
                                    'OR ',
                                    'DESC ', 
                                    'ASC ', 
                                    ' on (',
                                    ' on ',
                                    'having ',
                                    'group by '
                                  ), 
                            array ( '',
                                    ' $this->db->select("', 
                                    " UPDATE ", 
                                    " DELETE ", 
                                    " INSERT ", 
                                    " INTO ", 
                                    " VALUES ", 
                                    ' $this->db->from("', 
                                    " LEFT ",
                                    ' RIGHT ',
                                    ' INNER ', 
                                    ' $this->db->join("', 
                                    ' $this->db->where("', 
                                    ' $this->db->limit("', 
                                    ' $this->db->order_by("', 
                                    " AND ", 
                                    " OR ", 
                                    " DESC ", 
                                    " ASC ", 
                                    '", "',
                                    '", "',
                                    ' $this->db->having("',
                                    ' $this->db->group_by("'
                                  ), 
                            $sql 
                          ); 
		$final = array();

		foreach($sql as $row)
		{

			if (strpos($row, 'LEFT') !== FALSE)
			{
				$row     = str_replace('LEFT', '', $row);
				$final[] = trim($row) . '", "left");';
			}
			elseif (strpos($row, 'RIGHT') !== FALSE)
			{
				$row     = str_replace('RIGHT', '', $row);
				$final[] = trim($row) . '", "right");';
			}
			elseif (strpos($row, 'INNER') !== FALSE)
			{
				$row     = str_replace('INNER', '', $row);
				$final[] = trim($row) . '", "inner");';
			}
			elseif (strpos($row, 'OUTTER') !== FALSE)
			{
				$row     = str_replace('OUTTER', '', $row);
				$final[] = trim($row) . '", "outter");';
			}
			else
			{				
				$final[] = trim($row) . '");';
			}			
		}


		$sql = implode("<br>", $final);
		$sql .= '<br> $this->db->get();';

		return $sql;

	}


}