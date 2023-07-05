<?php

defined('ROOT_DIR') or die;

Events::exec("@body:beforeAfter");

?>
	
	<!-- << default >> -->
	<script src='<?php echo Core::url(ASSETS_DIR . '/js/jquery-3.6.4.min.js'); ?>'></script>
	<?php echo self::include_libraries('body', $exclude_libraries) . "\n"; ?>
	<script src='<?php echo Core::url(ASSETS_DIR . '/js/main.js'); ?>'></script>
	<!-- << default />> -->
	
<?php Events::exec('@body:after'); ?>
		
</body>
</html>