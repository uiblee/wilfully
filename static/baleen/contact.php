<?php
// Required for CAPTCHA
session_start();

// Error Handling
error_reporting( 0 );

// Validation Methods

// General Validation
function is_validate( $var ) {
	
	if( empty( $var ) ) {
		return 0;
	}
	
	return 1;
	
}

// Email Validation
function is_validate_email( $email ) {
	
	if( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		return 0;
	}

	return 1;

}

// CAPTCHA Validation
function is_validate_captcha( $code ) {

	// CAPTCHA Class
	include_once( dirname( __FILE__ ) . '/lib/securimage/securimage.php' );
	$securimage = new Securimage();

	if ( false == $securimage->check( $code ) ) {
	  return 0;
	}

	return 1;

}

/**
 * Email Parameters
 * Please customize these variables to fit your need
 */		
$to_name = 'Your Name';
$to_email = 'youremail@gmail.com';		
$subject = 'Baleen Contact Form';
/** End Email Parameters */

// Core Variables	
$response = array();
$validation = true;
$message = '<strong>Thanks!</strong> Your message has been submitted successfully.';
$message_error = '<strong>Thanks!</strong> Fix the following erros and try submitting again.';
$class = 'alert-success';
$class_error = 'alert-danger';

// Server Validation
if( $_POST ) {

	// Name Validation
	if( ! is_validate( $_POST['fullname'] ) ) {	
		$validation = false;
		$response['fullname'] = false;		
	}
	
	// Email Validation
	if( ! is_validate_email( $_POST['email'] ) ) {		
		$validation = false;
		$response['email'] = false;	
	}
	
	// Message Validation
	if( ! is_validate( $_POST['message'] ) ) {	
		$validation = false;
		$response['message'] = false;		
	}

	// CAPTCHA Validation
	if( ! is_validate_captcha( $_POST['captcha'] ) ) {	
		$validation = false;
		$response['captcha'] = false;	
	}
	
	// Let Process the Mail
	if( $validation == true ) {
		
		// Email Body
		$body = '<p>Hello '. $to_name .', You have received a message.<br /><br />
		<strong>Message:</strong><br /><br />
		'.nl2br( $_POST['message'] ).'<br /><br />
		<strong>From</strong>:<br /><br />
		'. $_POST['fullname'] .'<br />
		'. $_POST['email'] .'
		</p>';		
		
		// Mail Headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'To: '. $to_name .' <'. $to_email .'>' . "\r\n";
		$headers .= 'From: '. $_POST['fullname'] .' <'. $_POST['email'] .'>' . "\r\n";
		$headers .= 'Reply-To: '. $_POST['email'] .'' . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		
		if ( ! mail( $to_email, $subject, $body, $headers ) ) {			
			$class = 'alert-danger';
			$message = '<strong>Oh snap!</strong> We are unable to process your request.';		
		}
	
	} else {

		$message = $message_error;
		$class = $class_error;

	}	

}

// Validation Response
$response['validation'] = $validation;

// Server Response
$response['server'] = '<div class="alert '. $class .'"><button type="button" class="close" data-dismiss="alert">&times;</button>'. $message .'</div>';
echo json_encode( $response );
exit;