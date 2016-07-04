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
	$Parsedown = new Parsedown();
	return $Parsedown->text($text);
}

// 將如「[longitude]」類的文字替換為真實信息
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
require_once('validationFunc.php');
