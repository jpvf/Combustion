<div class="box full">
	<h4>Form para nuevo registro - Campos</h4>
	<div class="box-content">
	<?php echo form_open('generar/base/segundo_paso', array('class' => 'forms'));?>
		<ul class='list'>
<li>
<?php echo form_label('Nombre', 'nombre'); ?>
 <?php echo form_input(array('name' => 'nombre', 'id' => 'nombre', 'maxlength' => '15')); ?>
</li>
<li>
<?php echo form_label('Descripcion', 'descripcion'); ?>
 <?php echo form_textarea(array('name' => 'descripcion', 'id' => 'descripcion')); ?>
</li>
<li>
<?php echo form_label('Version', 'version'); ?>
 <?php echo form_input(array('name' => 'version', 'id' => 'version', 'maxlength' => '3')); ?>
</li>
<li>
 <?php echo form_submit(array('name' => 'submit', 'value' => 'Guardar', 'class' => 'button align-right')); ?>
</li>
</ul>	
	<?php echo form_close(); ?>
	</div>
</div>