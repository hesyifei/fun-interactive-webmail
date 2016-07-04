<?php
// 檢查用戶輸入的座標是否於某正確座標的附近
function checkCoordinates($coordinates, $arr) {
	if(is_numeric($arr[2])){
		$accuracy = floatval($arr[2]);
	}else{
		$accuracy = 0.0;
	}

	$correctLatitude = floatval($arr[0]);
	$correctLongitude = floatval($arr[1]);


	//緯度,經度
	sscanf($coordinates, "%f,%f", $latitude, $longitude);
	// 显示类型和值
	//var_dump($latitude, $longitude);
	//var_dump($arr);
	if(empty($latitude) || empty($longitude)){
		return false;
	}

	if(testNumberInAccuracy($latitude, $correctLatitude, $accuracy) && testNumberInAccuracy($longitude, $correctLongitude, $accuracy)){
		return true;
	}
	return false;
}
