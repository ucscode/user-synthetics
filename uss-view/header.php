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
		echo "<script>const Uss = JSON.parse(atob('{$console}'));</script>\n";
	?>
	
<?php Events::exec('@head::before'); ?>
	
	<!-- << defaults >> -->
	<?php echo self::include_libraries( 'head', $exclude_libraries ) . "\n"; ?>
	<link rel='stylesheet' href='<?php echo Core::url( ASSETS_DIR . '/css/main.css' ); ?>'>
	<!-- << defaults />> -->
	
<?php Events::exec('@head::after'); ?>
	
</head>

<body <?php if( is_array(Uss::$global['body.attrs'] ?? null) ) echo Core::array_to_html_attrs( Uss::$global['body.attrs'] ); ?>>
	
<?php Events::exec("@body::before"); ?>
