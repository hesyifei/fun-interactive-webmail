<?php
function getCurrentTime() {
	return date('H:i');
}

function check($key, $array) {
	if(array_key_exists($key, $array)) {
		if (is_null($array[$key])) {
			return false;
		} else {
			return true;
		}
	}
}

function testRange($val, $min, $max) {
	return ($val >= $min && $val <= $max);
}

function parsedownText($text) {
	$Parsedown = new Parsedown();
	return $Parsedown->text($text);
}

function generateMailData($name, $time, $content) {
	return [
		"id" => null,
		"sender" => $name,
		"time" => $time,
		"content" => $content
	];
}

function generateMailDataWithID($id, $name, $time, $content) {
	return [
		"id" => $id,
		"sender" => $name,
		"time" => $time,
		"content" => $content
	];
}

function getMailContentHTML($contentData, $shouldDelay = false) {
	?>
<div class="mail-content sender-<?php echo $contentData['sender']; ?><?php if($shouldDelay == true){ echo ' should-delay'; } ?>" id="<?php if(check('id', $contentData)){ echo 'checkpoint-'.$contentData['id'].'-mail'; } else { echo 'custom-mail'; } ?>" style="<?php if($shouldDelay == true){ echo 'display: none;'; } ?>">
	<p class="time"><?php echo $contentData["sender"]." ".$contentData["time"]; ?></p>
	<p><?php echo $contentData["content"]; ?></p>
</div>
	<?php
}

require_once('validationFunc.php');