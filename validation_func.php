<?php
// 檢查用戶輸入的座標是否於某正確座標的附近
function checkCoordinates($coordinates, $arr) {
	// 如果$accuracy傳入
	if(is_numeric($arr[2])){
		$accuracy = floatval($arr[2]);
	}else{
		$accuracy = 0.0;
	}

	// $arr內傳入正確座標
	$correctLatitude = floatval($arr[0]);
	$correctLongitude = floatval($arr[1]);


	// 格式：緯度,經度
	sscanf($coordinates, "%f,%f", $latitude, $longitude);
	//var_dump($latitude, $longitude);
	//var_dump($correctLatitude, $correctLongitude);
	//print_r($arr);

	// 如果沒有正確傳入經緯度
	if(empty($latitude) || empty($longitude)){
		return false;
	}

	// 如果傳入的經緯度在正確的經緯度±$accuracy內
	if(testNumberInAccuracy($latitude, $correctLatitude, $accuracy) && testNumberInAccuracy($longitude, $correctLongitude, $accuracy)){
		return true;
	}

	return false;
}
