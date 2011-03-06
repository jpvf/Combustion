<?php echo $doctype; ?>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $titulo ?></title>
		<link href="<?php echo $theme; ?>/assets/css/reset.css" rel="stylesheet" />
		<link href="<?php echo $theme; ?>/assets/css/smoothness/jquery-ui.css" rel="stylesheet" />
		<link href="<?php echo $theme; ?>/assets/css/style.css" rel="stylesheet" />
	</head>
	<body>
		<div id="container">
			<div id="header">

				<ul id="menu"> 
					<li><?php echo anchor('', 'Inicio'); ?></li>
					<li><a href="#">Menu del item</a></li>
					<li><a href="#">Menu del item</a></li>
					<li><a href="#">Menu del item</a></li>
				</ul>	

			</div>
			<div id="content">
			 <?php if($this->session->flashdata('mensaje')): ?>
			 	<?php $mensaje = $this->session->flashdata('mensaje'); ?>
			 	<div class='<?php echo $mensaje['tipo']; ?>'>
			 		<?php echo $mensaje['mensaje'] ; ?>
			 	</div>
			 <?php endif; ?>
			
			<?php echo $vista; ?>

			</div> <!-- Fin del Content -->

			<div class="push"></div>
		</div>

		<span class="clearFix"></span>
		
		<div id="footer">
			<p>Generador de código.</p>
		</div>
		
		<script src="<?php echo $theme; ?>/assets/js/jquery.min.js"></script>
		<script src="<?php echo $theme; ?>/assets/js/jquery-ui.min.js"></script>
		<script src="<?php echo $theme; ?>/assets/js/core.js"></script>

	</body>
</html>