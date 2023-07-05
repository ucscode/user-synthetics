<?php defined('ROOT_DIR') or die; ?>
<!doctype html>
<html>
	<head>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
		<style>
			* { 
				box-sizing: border-box;
			}
			body {
				padding: 0;
				margin: 0;
			}
			.container {
				text-align: center;
				font-family: calibri;
				max-width: 920px;
				margin: auto;
				padding: 2rem;
			}
			.info {
				font-size: 1.6rem;
				line-height: 1.9;
				padding: 1.2rem;
				word-break: break-word;
				font-family: Tahoma;
			}
			.php {
				color: blue;
			}
			.version {
				color: coral;
			}
			.project-name {
				text-shadow: 0 0px 1px #6a6666;
				font-size: 2em;
				color: cornflowerblue;
			}
			.star {
				margin: 3rem;
				padding: 0;
				line-height: 0;
				font-size: 5em;
				color: gold;
			}
			.prename {
				padding: 0 1.2rem;
			}
		</style>
	</head>
	<body>
		<div class='container'>
			<p class='star'>&#9733;</p>
			<h4 class='prename'>
				<span class='project-name'> <?php echo PROJECT_NAME; ?> </span>
			</h4>
			<div class='info'>
				MINIMUM OF <span class='php'>PHP</span> <span class='version'><?php echo MIN_PHP_VERSION; ?></span> IS REQUIRED FOR PROPER FUNCTIONALITY!
			</div>
		</div>
	</body>
</html>