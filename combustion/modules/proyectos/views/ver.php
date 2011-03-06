
	<div class='box full'>
		<h4>Proyectos</h4>
		<div class='box-content'>
			<?php echo anchor('proyectos/nuevo', 'Nuevo', array('class' => 'button')); ?>
			<?php echo br(3); ?>
			<?php echo $resultados;?>	
			<?php echo $paginacion; ?>
		</div>
	</div>