<?php

class forms
{

	function __construct()
	{
		$CI =& get_instance();
		$this->submit = "\t\t<?php echo form_submit(array('name' => 'submit', 'value' => 'Guardar', 'class' => 'button align-right')); ?>";
	}
	
	private function _create($fields = array())
	{
		$elements = array();
		
		foreach ($fields as $field)
		{
			if ( ! $field->primary_key AND $field->name != 'creado')
			{
				$elements[] = $this->_get_html_element($field->name, $field->type, $field->max_length);				
			}
		}
		
		return $elements;
			
	}
	
	private function _get_html_element($name = '', $type = '', $length = 0)
	{
		$element = '';
		
		switch($type)
		{
			case 'string':
				$value = '';
				if ($this->edit === TRUE)
				{
					$value = " , 'value' => \$row->$name ";
				}
				$element = "\t\t<?php echo form_input(array('name' => '$name', 'id' => '$name', 'maxlength' => '$length' $value)); ?>";			
				break;
			case 'blob':
				$value = '';
				if ($this->edit === TRUE)
				{
					$value = " , 'value' => \$row->$name ";
				}
				$element = "\t\t<?php echo form_textarea(array('name' => '$name', 'id' => '$name' $value)); ?>";
				break;
		}
		
		$element = "\t<?php echo form_label('" . ucwords($name) . "', '$name'); ?>\n " . $element ;
		
		return $element;
	}
	
	function generate($fields = '', $edit = FALSE)
	{
		$this->edit = $edit;
		
		$elements = $this->_create($fields);
		
		$ul = "\n<ul class='list'>\n\t<li>\n\t";

		$ul .= implode("\n\t</li>\n\t<li>\n\t", $elements);
		
		$ul .= "\n\t</li>\n\t<li>\n " . $this->submit;
		
		$ul .= "\n\t</li>\n</ul>\n";		
		
		return $ul;
	}

}