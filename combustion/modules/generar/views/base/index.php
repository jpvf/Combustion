<div class="box full">
	<h4>Generar desde base de datos</h4>
		<div class="box-content">
			<?php echo form_open('generar/base/primer_paso', array('class' => 'forms')); ?>
				<ul class="list">
					<li>
						<?php echo form_label('Seleccione una tabla: ','tabla'); ?>
						<?php echo form_dropdown('tabla', $tablas); ?>
					</li>
					<li class='even'>
						<?php echo form_label('Escriba una consulta: ','consulta'); ?>
						<?php echo form_textarea(array('name' => 'consulta', 'id' => 'consulta')); ?>
					</li>
					<li>
						<?php echo form_hidden('paso', '1'); ?>
						<?php echo form_submit(array('value' => 'Siguiente', 'class' => 'button align-right')); ?>
					</li>
				</ul>
			<?php echo form_close(); ?>
		</div>
</div>