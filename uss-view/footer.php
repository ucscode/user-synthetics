<?php 

defined( 'ROOT_DIR' ) OR DIE;

events::exec("@body::beforeAfter"); 
	
?>
	
	<!-- << default >> -->
	<script src='<?php echo core::url( ASSETS_DIR . '/js/jquery-3.6.4.min.js' ); ?>'></script>
	<?php echo self::include_libraries( 'body', $exclude_libraries ) . "\n"; ?>
	<script src='<?php echo core::url( ASSETS_DIR . '/js/main.js' ); ?>'></script>
	<!-- << default />> -->
	
<?php events::exec('@body::after'); ?>
		
</body>
</html>