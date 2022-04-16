<?php
/*
Plugin Name: Admin hCaptcha
Plugin URI: https://github.com/ChrisChrome/Admin-hCaptcha.git
Description: This plugin enable hCaptcha on Admin login screen
Version: 1.3
Author: Original by Abdul Rauf, rework by Chris Chrome
Author URI: https://github.com/ChrisChrome
*/

if( !defined( 'YOURLS_ABSPATH' ) ) die();

yourls_add_action( 'pre_login_username_password', 'abdulrauf_adminhCaptcha_validatehCaptcha' );

// Validates hCaptcha
function abdulrauf_adminhCaptcha_validatehCaptcha()
{
	include('captcha.php'); 
	if ($resp != null && $resp->success) 
	{ 
		//hCaptcha validated
		return true;
	}
	else
	{
		yourls_do_action( 'login_failed' );
		yourls_login_screen( $error_msg = 'hCaptcha validation failed' );
		die();
		return false;
	}
}

// Register plugin on admin page
yourls_add_action( 'plugins_loaded', 'abdulrauf_adminhCaptcha_init' );
function abdulrauf_adminhCaptcha_init() {
    yourls_register_plugin_page( 'adminhCaptcha', 'Admin hCaptcha Settings', 'adminhCaptcha_config_page' );
}

// The function that will draw the config page
function adminhCaptcha_config_page() {
    	 if( isset( $_POST['abdulrauf_adminhCaptcha_public_key'] ) ) {
	        yourls_verify_nonce( 'abdulrauf_adminhCaptcha_nonce' );
	        abdulrauf_adminhCaptcha_save_admin();
	    }
    
    $nonce = yourls_create_nonce( 'abdulrauf_adminhCaptcha_nonce' );
    $pubkey = yourls_get_option( 'abdulrauf_adminhCaptcha_pub_key', "" );
    $privkey = yourls_get_option( 'abdulrauf_adminhCaptcha_priv_key', "" );
    echo '<h2>Admin hCaptcha plugin settings</h2>';
    echo '<form method="post">';
    echo '<input type="hidden" name="nonce" value="' . $nonce . '" />';
    echo '<p><label for="abdulrauf_adminhCaptcha_public_key">hCaptcha site key: </label>';
    echo '<input type="text" id="abdulrauf_adminhCaptcha_public_key" name="abdulrauf_adminhCaptcha_public_key" value="' . $pubkey . '"></p>';  
    echo '<p><label for="abdulrauf_adminhCaptcha_private_key">hCaptcha secret key: </label>';
    echo '<input type="text" id="abdulrauf_adminhCaptcha_private_key" name="abdulrauf_adminhCaptcha_private_key" value="' . $privkey . '"></p>';
    echo '<input type="submit" value="Save"/>';
    echo '</form>';

}

// Save hCaptcha keys in database 
function abdulrauf_adminhCaptcha_save_admin()
{
	$pubkey = $_POST['abdulrauf_adminhCaptcha_public_key'];
	$privkey = $_POST['abdulrauf_adminhCaptcha_private_key'];
	if ( yourls_get_option( 'abdulrauf_adminhCaptcha_pub_key' ) !== false ) {
        yourls_update_option( 'abdulrauf_adminhCaptcha_pub_key', $pubkey );
    } 
	else {
        yourls_add_option( 'abdulrauf_adminhCaptcha_pub_key', $pubkey );
    }
	if ( yourls_get_option( 'abdulrauf_adminhCaptcha_priv_key' ) !== false ) {
        yourls_update_option( 'abdulrauf_adminhCaptcha_priv_key', $privkey );
    } 
	else {
        yourls_add_option( 'abdulrauf_adminhCaptcha_priv_key', $privkey );
    }
    echo "Saved";
}

// Add the JavaScript for hCaptcha widget
yourls_add_action( 'html_head', 'abdulrauf_adminhCaptcha_addjs' );
function abdulrauf_adminhCaptcha_addjs() {
	$siteKey = yourls_get_option( 'abdulrauf_adminhCaptcha_pub_key' );
	?>
	<script type="text/javascript">
	//JQuery function to add div for hCaptcha widget and load js only on login screen
	$(document).ready(function() {
		var logindiv = document.getElementById('login');
		if (logindiv != null) { //check if we are on login screen
			//getting hCaptcha script by jquery only on login screen
			$.getScript( "https://js.hcaptcha.com/1/api.js?onload=loadCaptcha&render=explicit");
			var form = logindiv.innerHTML;
			var index = form.indexOf('<p style="text-align: right;">'); //finding tag before which hCaptcha widget should appear
			document.getElementById('login').innerHTML = form.slice(0, index) + '<div id="captcha_container"></div>' + form.slice(index);	    
		}
    });
	// JavaScript function to explicitly render the hCaptcha widget
	var loadCaptcha = function() {
	  captchaContainer = ghCaptcha.render('captcha_container', {
		'sitekey' : '<?php echo $siteKey?>'
	  });
	};
	</script>
	<?php
}
?>
