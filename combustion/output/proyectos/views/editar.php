
	<div class='box full'>
		<h4>Proyectos</h4>
		<div class='box-content'>
			<?php echo form_open('proyectos/editar', array('class' => 'forms', 'id' => 'nuevo-proyectos'));?>
				
<ul class='list'>
	<li>
		<?php echo form_label('Nombre', 'nombre'); ?>
 		<?php echo form_input(array('name' => 'nombre', 'id' => 'nombre', 'maxlength' => '15'  , 'value' => $row->nombre )); ?>
	</li>
	<li>
		<?php echo form_label('Descripcion', 'descripcion'); ?>
 		<?php echo form_textarea(array('name' => 'descripcion', 'id' => 'descripcion'  , 'value' => $row->descripcion )); ?>
	</li>
	<li>
		<?php echo form_label('Version', 'version'); ?>
 		<?php echo form_input(array('name' => 'version', 'id' => 'version', 'maxlength' => '3'  , 'value' => $row->version )); ?>
	</li>
	<li>
 		<?php echo form_submit(array('name' => 'submit', 'value' => 'Guardar', 'class' => 'button align-right')); ?>		<?php echo anchor('proyectos/index', 'Cancelar', array('class' => 'button align-right')); ?>
	</li>
</ul>

				<?php echo form_hidden('id', $id); ?>
			<?php echo form_close(); ?>	
			<?php echo br(3); ?>
			<?php echo anchor('proyectos/index', 'Volver', array('class' => 'button')); ?>
			<?php echo anchor("proyectos/detalles/$id", 'Ver', array('class' => 'button')); ?>
			<?php echo anchor("proyectos/eliminar/$id", 'Eliminar', array('class' => 'button')); ?>			
		</div>
	</div>	