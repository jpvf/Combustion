
	<div class='box full'>
		<h4>Proyectos</h4>
		<div class='box-content'>
			<?php echo form_open('proyectos/nuevo', array('class' => 'forms', 'id' => 'nuevo-proyectos'));?>
				
<ul class='list'>
	<li>
		<?php echo form_label('Nombre', 'nombre'); ?>
 		<?php echo form_input(array('name' => 'nombre', 'id' => 'nombre', 'maxlength' => '15' )); ?>
	</li>
	<li>
		<?php echo form_label('Descripcion', 'descripcion'); ?>
 		<?php echo form_textarea(array('name' => 'descripcion', 'id' => 'descripcion' )); ?>
	</li>
	<li>
		<?php echo form_label('Version', 'version'); ?>
 		<?php echo form_input(array('name' => 'version', 'id' => 'version', 'maxlength' => '3' )); ?>
	</li>
	<li>
 		<?php echo form_submit(array('name' => 'submit', 'value' => 'Guardar', 'class' => 'button align-right')); ?>		<?php echo anchor('proyectos/index', 'Cancelar', array('class' => 'button align-right')); ?>
	</li>
</ul>

			<?php echo form_close(); ?>	
		</div>
	</div>	