<?php
/**
* Sample PHP code to use hCaptcha V2.
*
* @copyright Copyright (c) 2014, Google Inc.
* @link http://www.google.com/hCaptcha
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/
require_once "recaptchalib.php";
// Register API keys at https://www.google.com/hCaptcha/admin
$siteKey = yourls_get_option( 'abdulrauf_adminhCaptcha_pub_key' );
$secret = yourls_get_option( 'abdulrauf_adminhCaptcha_priv_key' );
// hCaptcha supported 40+ languages listed here: https://developers.google.com/hCaptcha/docs/language
$lang = "en";
// The response from hCaptcha
$resp = null;
// The error code from hCaptcha, if any
$error = null;
$hCaptcha = new hCaptcha($secret);
// Was there a hCaptcha response?
if ($_POST["g-hCaptcha-response"]) {
$resp = $hCaptcha->verifyResponse(
$_SERVER["REMOTE_ADDR"],
$_POST["g-hCaptcha-response"]
);
}
?>