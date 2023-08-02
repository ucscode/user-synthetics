<?php

defined('ROOT_DIR') or die;

Events::exec("@body:beforeAfter");

?>
	
	<!-- << default >> -->
	<?php echo self::include_libraries('body', $exclude_libraries) . "\n"; ?>
	<!-- << default />> -->
	
<?php Events::exec('@body:after'); ?>
		
</body>
</html>