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

require_once('validationFunc.php');