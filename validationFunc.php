<?php
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

	if(testRange($latitude, $correctLatitude-$accuracy, $correctLatitude+$accuracy) && testRange($longitude, $correctLongitude-$accuracy, $correctLongitude+$accuracy)){
		return true;
	}
	return false;
}
