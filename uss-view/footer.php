<?php 

defined( 'ROOT_DIR' ) OR DIE;

events::exec("@body::beforeAfter"); 
	
?>
	
	<!-- << default >> -->
	
	<!-- [[jQuery](https://jquery.com)]: jQuery is a free and open-source JavaScript library designed to simplify HTML DOM tree traversal, event handling, and animation in web applications -->
	<script src='<?php echo core::url( ASSETS_DIR . '/js/jquery-3.6.4.min.js' ); ?>'></script>
	
	<!-- [[Bootstrap](@see header)] -->
	<script src='<?php echo core::url( ASSETS_DIR . '/js/bootstrap.bundle.min.js' ); ?>'></script>
	
	<!-- [[BootBox](https://bootboxjs.com)]: Bootbox.js is a free and open-source JavaScript library for creating dialog boxes and alerts that are styled and customizable using Bootstrap -->
	<script src='<?php echo core::url( ASSETS_DIR . '/js/bootbox.all.min.js' ); ?>'></script>
	
	<!-- [[Notiflix](https://notiflix.github.io)]: Notiflix is a free and open-source library for creating responsive and customizable notifications, alerts, and modals in web applications using JavaScript and CSS -->
	<script src='<?php echo core::url( ASSETS_DIR . '/vendor/notiflix/notiflix-loading-aio-3.2.6.min.js' ); ?>'></script>
	<script src='<?php echo core::url( ASSETS_DIR . '/vendor/notiflix/notiflix-block-aio-3.2.6.min.js' ); ?>'></script>
	
	<!-- [[Toastr](@see header)] -->
	<script src='<?php echo core::url( ASSETS_DIR . '/vendor/toastr/toastr.min.js' ); ?>'></script>
	
	<!-- <<<?php echo PROJECT_NAME; ?>>>: The standard <?php echo PROJECT_NAME; ?> JavaScript -->
	<script src='<?php echo core::url( ASSETS_DIR . '/js/main.js' ); ?>'></script>
	
	<!-- << default />> -->
	
<?php events::exec('@body::after'); ?>
		
</body>
</html>