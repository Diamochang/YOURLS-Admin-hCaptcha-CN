<?php
/**
* This is a PHP library that handles calling hCaptcha.
* - Documentation and latest version
* https://developers.google.com/hCaptcha/docs/php
* - Get a hCaptcha API Key
* https://www.google.com/hCaptcha/admin/create
* - Discussion group
* http://groups.google.com/group/hCaptcha
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
/**
* A hCaptchaResponse is returned from checkAnswer().
*/
class hCaptchaResponse
{
public $success;
public $errorCodes;
}
class hCaptcha
{
private static $_signupUrl = "https://dashboard.hcaptcha.com";
private static $_siteVerifyUrl =
"https://hcaptcha.com/siteverify?";
private $_secret;
private static $_version = "php_1.0";
/**
* Constructor.
*
* @param string $secret shared secret between site and hCaptcha server.
*/
function hCaptcha($secret)
{
if ($secret == null || $secret == "") {
die("To use hCaptcha you must get an API key from <a href='"
. self::$_signupUrl . "'>" . self::$_signupUrl . "</a>");
}
$this->_secret=$secret;
}
/**
* Encodes the given data into a query string format.
*
* @param array $data array of string elements to be encoded.
*
* @return string - encoded request.
*/
private function _encodeQS($data)
{
$req = "";
foreach ($data as $key => $value) {
$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
}
// Cut the last '&'
$req=substr($req, 0, strlen($req)-1);
return $req;
}
/**
* Submits an HTTP GET to a hCaptcha server.
*
* @param string $path url path to hCaptcha server.
* @param array $data array of parameters to be sent.
*
* @return array response
*/
private function _submitHTTPGet($path, $data)
{
$req = $this->_encodeQS($data);
$response = file_get_contents($path . $req);
return $response;
}
/**
* Calls the hCaptcha siteverify API to verify whether the user passes
* CAPTCHA test.
*
* @param string $remoteIp IP address of end user.
* @param string $response response string from hCaptcha verification.
*
* @return hCaptchaResponse
*/
public function verifyResponse($remoteIp, $response)
{
// Discard empty solution submissions
if ($response == null || strlen($response) == 0) {
$hCaptchaResponse = new hCaptchaResponse();
$hCaptchaResponse->success = false;
$hCaptchaResponse->errorCodes = 'missing-input';
return $hCaptchaResponse;
}
$getResponse = $this->_submitHttpGet(
self::$_siteVerifyUrl,
array (
'secret' => $this->_secret,
'remoteip' => $remoteIp,
'v' => self::$_version,
'response' => $response
)
);
$answers = json_decode($getResponse, true);
$hCaptchaResponse = new hCaptchaResponse();
if (trim($answers ['success']) == true) {
$hCaptchaResponse->success = true;
} else {
$hCaptchaResponse->success = false;
$hCaptchaResponse->errorCodes = $answers [error-codes];
}
return $hCaptchaResponse;
}
}
?>