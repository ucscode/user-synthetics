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
	
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		
		<!-- [[Bootstrap](https://getbootstrap.com)]: A popular open-source framework for building responsive and mobile-first websites and web applications -->
		<link rel='stylesheet' href='<?php echo core::url( ASSETS_DIR . '/css/bootstrap.min.css' ); ?>'>
		
		<!-- [[Animate](https://animate.style)]: Animate.css is a popular CSS animation library that provides pre-built animation classes for adding animations to web projects -->
		<link rel='stylesheet' href='<?php echo core::url( ASSETS_DIR . '/css/animate.min.css' ); ?>'>
		
		<!-- [[Bootstrap Icon](https://icons.getbootstrap.com)]: Bootstrap Icons is a free and open-source icon library for web development, featuring hundreds of icons in a variety of categories -->
		<link rel='stylesheet' href='<?php echo core::url( ASSETS_DIR . '/vendor/bootstrap-icons/bootstrap-icons.css' ); ?>'>
		
		<!-- [[Toastr](https://github.com/CodeSeven/toastr)]: Toastr.js is a lightweight and customizable JavaScript library for displaying notifications or toasts on web pages. -->
		<link rel='stylesheet' href='<?php echo core::url( ASSETS_DIR . '/vendor/toastr/toastr.min.css' ); ?>'>
		
		<!-- <<<?php echo PROJECT_NAME; ?>>>: The standard <?php echo PROJECT_NAME; ?> Stylesheet -->
		<link rel='stylesheet' href='<?php echo core::url( ASSETS_DIR . '/css/main.css' ); ?>'>
	
	<!-- << defaults />> -->
	
<?php events::exec('@head::after'); ?>
	
</head>

<body <?php if( is_array(uss::$global['body.attrs'] ?? null) ) echo core::array_to_html_attrs( uss::$global['body.attrs'] ); ?>>
	
<?php events::exec("@body::before"); ?>
