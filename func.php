<?php
// 以「05:20」類的格式獲取目前時間
function getCurrentTime() {
	return date('H:i');
}

// 檢查$array某個$key是否存在（不為null）
function check($key, $array) {
	if(array_key_exists($key, $array)) {
		if (is_null($array[$key])) {
			return false;
		} else {
			return true;
		}
	}
}

// 檢查$val是否處於$min至$max的範圍內
function testRange($val, $min, $max) {
	return ($val >= $min && $val <= $max);
}

// 檢查$val是否在$correctVal的±$accuracy的範圍內
function testNumberInAccuracy($val, $correctVal, $accuracy) {
	return testRange($val, $correctVal-$accuracy, $correctVal+$accuracy);
}

// 格式化文字
function parsedownText($text) {
	$Parsedown = new ParsedownExtra();
	return $Parsedown->text($text);
}

// 將如「[next_longitude]」類的文字替換為真實信息
function replacePlaceholderWithData($text, $checkpointIds) {
	// 解析checkpoint_data.json文件內容並儲存為array
	$checkpointData = json_decode(file_get_contents("checkpoint_data.json"), true);

	// 循環所有ID
	foreach($checkpointIds as $prefix => $checkpointId){
		// 如果檢查點信息Array中有該ID檢查點的信息
		if(check($checkpointId, $checkpointData)){
			//var_dump($checkpointData[$checkpointId]);
			// 循環該ID檢查點信息
			foreach($checkpointData[$checkpointId] as $key => $data){
				// 將「[$key]」替換為具體信息
				$text = str_replace("[".$prefix."_".$key."]", $data, $text);
				//var_dump($text);
			}
		}
	}

	// 匹配類似「[func:name{para1,para2}]」類的字串
	$funcMatchesNumber = preg_match_all('/\[func:(.+?){(.*?)}\]/', $text, $funcMatches);
	/*echo '<pre>';
	print_r($funcMatches);
	echo '</pre>';*/
	// 循環所有的func字串
	for($i = 0; $i <= $funcMatchesNumber-1; $i++){
		// 如果function名稱不為空
		if(!empty(trim($funcMatches[1][$i]))){
			$funcName = trim($funcMatches[1][$i]);
			// 如果該function存在
			if(function_exists($funcName)){
				// 刪除將傳入參數的額外空格
				$funcPara = trim($funcMatches[2][$i]);
				// 用,分割將傳入的參數
				$funcParaArr = explode(",", $funcPara);
				// 呼叫函數
				$returnFuncText = call_user_func($funcName, $funcParaArr);
				// 將函數的[func...]替換為函數所回傳的字串
				$text = str_replace($funcMatches[0][$i], $returnFuncText, $text);
			}
		}
	}

	return $text;
}

// 批量替換所有「[next_longitude]」類文字為真實信息
function replaceWithAllCheckpointData($text, $checkpointId, $sequenceArr) {
	// 如果該checkpointId在sequenceArr內
	if(in_array(intval($checkpointId), $sequenceArr)){
		// 找到這是第N個checkpoint
		$sequenceKey = array_search(intval($checkpointId), $sequenceArr);

		$toBePassedCheckpointIds = [];
		$toBePassedCheckpointIds["current"] = intval($checkpointId);

		// 如果順序列表中還存在下一個checkpointId
		if(check($sequenceKey+1, $sequenceArr)){
			$toBePassedCheckpointIds["next"] = $sequenceArr[$sequenceKey+1];
		}

		// 替換部分信息
		return replacePlaceholderWithData($text, $toBePassedCheckpointIds);
	}
	return $text;
}

// 生成郵件數據
function generateMailData($name, $time, $content) {
	return [
		"id" => null,
		"sender" => $name,
		"time" => $time,
		"content" => $content
	];
}

// 生成帶ID的郵件數據
function generateMailDataWithID($id, $name, $time, $content) {
	return [
		"id" => $id,
		"sender" => $name,
		"time" => $time,
		"content" => $content
	];
}

// 生成郵件HTML
function getMailContentHTML($contentData, $shouldDelay = false) {
	?>
<div class="mail-content sender-<?php echo $contentData['sender']; ?><?php if($shouldDelay == true){ echo ' should-delay'; } ?>" id="<?php if(check('id', $contentData)){ echo 'checkpoint-'.$contentData['id'].'-mail'; } else { echo 'custom-mail'; } ?>" style="<?php if($shouldDelay == true){ echo 'display: none;'; } ?>">
	<p class="time"><?php echo $contentData["sender"]." ".$contentData["time"]; ?></p>
	<p><?php echo $contentData["content"]; ?></p>
</div>
	<?php
}

// 載入「驗證郵件回覆之用」的function所在文件
require_once('validation_func.php');

// 載入「處理回覆郵件內容」的function所在文件
require_once('mail_func.php');
