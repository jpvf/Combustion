
	<div class='box full'>
		<h4>Proyectos</h4>
		<div class='box-content'>
			<?php echo $details; ?>
			<?php echo br(3); ?>
			<?php echo anchor('proyectos/index', 'Volver', array('class' => 'button')); ?>
			<?php echo anchor("proyectos/editar/$id", 'Editar', array('class' => 'button')); ?>	
			<?php echo anchor("proyectos/eliminar/$id", 'Eliminar', array('class' => 'button')); ?>		
		</div>
	</div>	