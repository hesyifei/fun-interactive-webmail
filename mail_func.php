<?php
function calcRelativePosition($arr) {
	// 如果傳入3個必要參數
	if(count($arr) == 3){
		// 如果[0]和[1]是數字
		if(is_numeric($arr[0]) && is_numeric($arr[1])){
			$mainPosition = floatval($arr[0]);
			$newPosition = floatval($arr[1]);

			// 1度=111千米=111*1000米
			$metersDiff = ($newPosition-$mainPosition)*111*1000;

			switch($arr[2]){
				case 'latitude':
					$diffPosition = $metersDiff > 0 ? "北" : "南";
					break;
				case 'longitude':
					$diffPosition = $metersDiff > 0 ? "東" : "西";
					break;
				default:
					// ERROR
					$diffPosition = "(NEITHER_LAT/LON)";
					break;
			}

			// 回傳「[東/西/南/北][準確到2位小數的絕對值]米」
			return $diffPosition.sprintf("%.2f", abs($metersDiff))."米";
		}
	}
	// 如果函數參數不足/過多，則回傳錯誤
	return "_ERROR_";
}

// FOR TESTING ONLY
function mailTestFunc() {
	return "TEST";
}
