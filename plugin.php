<?php
/*
Plugin Name: YOURLS Admin hCaptcha CN
Plugin URI: https://github.com/Diamochang/YOURLS-Admin-hCaptcha-CN
Description: 通过在管理面板登录页面添加 hCaptcha 验证来保护你的 YOURLS 短链接服务。
Version: 1.3
Author: Abdul Rauf, rework by Mike Wang (Diamochang)
Author URI: http://imdchs.rf.gd
*/

if( !defined( 'YOURLS_ABSPATH' ) ) die();

yourls_add_action( 'pre_login_username_password', 'abdulrauf_adminhCaptcha_validatehCaptcha' );

// Validates hCaptcha
function abdulrauf_adminhCaptcha_validatehCaptcha()
{
	// 既然要用 hCaptcha，为什么要用 Google 的代码？奇怪。
	// include('captcha.php'); 
	// if ($resp != null && $resp->success) 
	// { 
	//	//hCaptcha validated
	//	return true;
	// }
	// else
	// {
	//	yourls_do_action( 'login_failed' );
	//	yourls_login_screen( $error_msg = 'hCaptcha validation failed' );
	//	die();
	//	return false;
	// }

	// 使用 https://medium.com/@hCaptcha/using-hcaptcha-with-php-fc31884aa9ea 中的验证方法
	$privKey = yourls_get_option( 'abdulrauf_adminhCaptcha_priv_key' );
	$vdata = array(
	    'secret' => $privKey,
            'response' => $_POST['h-captcha-response']
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($vdata));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        // var_dump($response);
        $responseData = json_decode($response);
        if($responseData->success) {
             return true;
        } 
        else {
             yourls_do_action( 'login_failed' );
	     yourls_login_screen( $error_msg = 'hCaptcha 验证失败。如果 hCaptcha 被你所在地区的 GFW 非完全屏蔽，请尝试使用代理或者使用加速镜像。残障人士也可访问 https://www.hcaptcha.com/accessibility 了解如何设置可以直接绕过的 Cookie。' );
	     die();
	     return false;
        }
}

// Register plugin on admin page
yourls_add_action( 'plugins_loaded', 'abdulrauf_adminhCaptcha_init' );
function abdulrauf_adminhCaptcha_init() {
    yourls_register_plugin_page( 'adminhCaptcha', 'Admin hCaptcha 设置', 'adminhCaptcha_config_page' );
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
    // 参考 Ozh 的写法，直接 echo 整个设置表单，提高效率
    echo <<<HTML
        <main>
            <h2>Admin hCaptcha 插件设置</h2>
            <form method="post">
            <input type="hidden" name="nonce" value="$nonce" />
            <p>
                <label>你从 hCaptcha 仪表板获得的站点密钥（sitekey）：</label>
	        <input type="text" id="abdulrauf_adminhCaptcha_public_key" name="abdulrauf_adminhCaptcha_public_key" value="$pubkey">
            </p>
	    <p>
                <label>你从 hCaptcha 仪表板获得的私钥（secret）：</label>
	        <input type="text" id="abdulrauf_adminhCaptcha_private_key" name="abdulrauf_adminhCaptcha_private_key" value="$privkey">
            </p>
            <p><input type="submit" value="保存设置" class="button" /></p>
            </form>
        </main>
    HTML;
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
    echo "设置已保存";
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
			// 按照官方方法调用 API
			$.getScript( "https://www.hcaptcha.com/1/api.js");
			var form = logindiv.innerHTML;
			var index = form.indexOf('<p style="text-align: right;">'); //finding tag before which hCaptcha widget should appear
			document.getElementById('login').innerHTML = form.slice(0, index) + '<div class="h-captcha" data-sitekey="<?php echo $siteKey?>"></div>' + form.slice(index);	    
		}
    });
	// JavaScript function to explicitly render the reCAPTCHA widget
	// 经测试，此种 render 在 hCaptcha 上无效。
	// var loadCaptcha = function() {
	//   captchaContainer = hcaptcha.render('captcha_container', {
	// 	'sitekey' : '<?php echo $siteKey?>'
	//   });
	// };
	</script>
	<?php
}
?>
