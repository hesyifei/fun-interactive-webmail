<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

date_default_timezone_set('Asia/Hong_Kong');

// 將session設定為很久以後過期
$sessionTime = 365 * 24 * 60 * 60;
$sessionName = "mail_session";
session_set_cookie_params($sessionTime);
session_name($sessionName);
session_start();

if (isset($_COOKIE[$sessionName])) {
	setcookie($sessionName, $_COOKIE[$sessionName], time() + $sessionTime, "/");
}
