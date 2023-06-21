<?php 
/*
 * Literally, we don't want this file printing error such as:
 * Uncaught Error: Class 'uss' not found in line ...
 */
defined( 'ROOT_DIR' ) OR DIE; 
	
?><!doctype html>
<html>
<head>
	
	<?php
		/*
		 * Pass variables from PHP to JavaScript environment
		 */
		$console = base64_encode( json_encode( (object)self::$console ) );
		echo "<script>const uss = JSON.parse(atob('{$console}'));</script>\n";
	?>
	
<?php events::exec('@head::before'); ?>
	
	<!-- << defaults >> -->
	<?php echo self::include_libraries( 'head', $exclude_libraries ) . "\n"; ?>
	<link rel='stylesheet' href='<?php echo core::url( ASSETS_DIR . '/css/main.css' ); ?>'>
	<!-- << defaults />> -->
	
<?php events::exec('@head::after'); ?>
	
</head>

<body <?php if( is_array(uss::$global['body.attrs'] ?? null) ) echo core::array_to_html_attrs( uss::$global['body.attrs'] ); ?>>
	
<?php events::exec("@body::before"); ?>
