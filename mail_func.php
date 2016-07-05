<?php
function calcRelativePosition($arr) {
	// 如果傳入3個必要參數
	if(count($arr) == 3){
		// 如果[0]和[1]是數字
		if(is_numeric($arr[0]) && is_numeric($arr[1])){
			$mainPosition = floatval($arr[0]);
			$newPosition = floatval($arr[1]);

			$degreeDiff = $newPosition-$mainPosition;

			switch($arr[2]){
				case 'latitude':
					$diffPosition = $degreeDiff > 0 ? "N" : "S";
					break;
				case 'longitude':
					$diffPosition = $degreeDiff > 0 ? "E" : "W";
					break;
				default:
					// ERROR
					$diffPosition = "(NEITHER_LAT/LON)";
					break;
			}

			// 回傳「[準確到6位小數的絕對值]°[N/S/E/W]」
			return sprintf("%.6f", abs($degreeDiff))."°".$diffPosition;
		}
	}
	// 如果函數參數不足/過多，則回傳錯誤
	return "_ERROR_";
}

// FOR TESTING ONLY
function mailTestFunc() {
	return "TEST";
}
