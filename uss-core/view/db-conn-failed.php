<?php defined('ROOT_DIR') or die; ?>

<div class='px-3 py-2 my-2 text-center'>

	<img class='d-block mx-auto mb-4' src='<?php echo $this->global['icon']; ?>' alt='user synthetics logo' height='76'>
	
	<h1 class='display-6 fw-light mb-2'>Database Connection Failed</h1>
	
	<h5 class='fw-light mb-4'>
		<i class='bi bi-arrow-right me-1 animate__animated animate__shakeX d-inline-block text-warning'></i> 
		<i class='text-muted'>%{connect_error}</i>
	</h5>
	
	<p class='text-danger' style='font-family: monospace;'>This might have happened because the MYSQL server has not yet started</p>
	
	<h5 class='text-muted'><u>Otherwise:</u></h5>
	
	<div class='col-lg-6 mx-auto text-muted'>
	
		<div class='fw-light mb-4 border-top pt-4'>
			<p>&mdash; Confirm that the information provided in your <strong>conn.php</strong> file is correct.</p>
			<p>&mdash; Ensure that the user have sufficient permission to manage database engine</p>
			<p>&mdash; Create database manually if it does not exist (PHPMyAdmin)</p>
		</div>
		
		<div class='border-top border-bottom mb-3'>
			<div class='py-4'>
			<p class='fs-5'>Need Help?</p>
			<a type='button' class='btn btn-primary px-4 gap-3' href='mailto:uche23mail@gmail.com'>
				Contact Developer
			</a>
			</div>
		</div>
		
		<div class='mb-2'>
			<div class='mb-1'> Powered By <a href='<?php echo $this->project_url; ?>' target='_blank'><?php echo PROJECT_NAME; ?></a> </div> 
			<div>&copy; 2022</div>
		</div>
		
	</div>
</div>

