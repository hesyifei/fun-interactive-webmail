<?php
// 開啟所有錯誤顯示
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 載入 PHP Composer 的內容
require 'vendor/autoload.php';

// 設定時區為香港
date_default_timezone_set('Asia/Hong_Kong');

// 將session設定為很久以後過期
// 詳見：http://stackoverflow.com/q/3684620/2603230
$sessionTime = 365 * 24 * 60 * 60;
$sessionName = "mail_session";
session_set_cookie_params($sessionTime);
session_name($sessionName);
session_start();

if (isset($_COOKIE[$sessionName])) {
    // 延長session的可用時間
	setcookie($sessionName, $_COOKIE[$sessionName], time() + $sessionTime, "/");
}
