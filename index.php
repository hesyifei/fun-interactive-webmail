<?php
require_once("init.php");
require_once("func.php");



// 解析mail_data.json文件內容並儲存為array
$mailData = json_decode(file_get_contents("mail_data.json"), true);
// 將部分信息儲存為單獨的變量中
$mailPrefixContentSequentially = $mailData["mail_prefix_content_sequentially"];
$directlyMailData = $mailData["mail_directly"];
$needReplyMailData = $mailData["mail_need_reply"];

// 將不會變信息的define成constant
define("senderName", $mailData["sender_name"]);
define("receiverName", $mailData["receiver_name"]);
//print_r($defaultReplyData);



// 如果還未創建該session
if(!isset($_SESSION["googleFormClosedArr"])){
	$_SESSION["googleFormClosedArr"] = [];
}
// 記錄$googleFormClosedArrId以確定這一檢查點的網頁的Google Form是否被關閉過（如果關閉過就不會再顯示）
if(empty($_GET["checkpointId"])){
	$googleFormClosedArrId = "default";
}else{
	$googleFormClosedArrId = $_GET["checkpointId"];
}
// 如果收到AJAX用POST傳輸提示Google Form已關閉的信息
if(!empty($_POST["set-google-form-closed-session"])){
	$_SESSION["googleFormClosedArr"][$googleFormClosedArrId] = true;
	echo '已設定$_SESSION["googleFormClosedArr"][$googleFormClosedArrId]為'.$_SESSION["googleFormClosedArr"][$googleFormClosedArrId];
	// 由於是AJAX請求，所以直接exit即可
	exit;
}


// 建立空$sequenceArr
$sequenceArr = [];
// 如果收到用戶用GET傳輸順序信息
if(!empty($_GET["sequence"])){
	// 將順序信息以int的方式儲存至$sequenceArr中
	$sequenceArr = array_map('intval', explode(',', $_GET["sequence"]));
	//var_dump($sequenceArr);
}


// 如果收到用戶用GET傳輸希望清空$_SESSION["displayData"]的信息
if(isset($_GET["unsetSession"])){
	unset($_SESSION["displayData"]);
}
// FOR TESTING
//unset($_SESSION["displayData"]);


// 如果$_SESSION["displayData"]為空
if(!isset($_SESSION["displayData"])){
	// 建立空$_SESSION["displayData"]
	$_SESSION["displayData"] = [];
	foreach($mailData["introduction_mail"] as $eachIntroMailData){
		$mailSenderName = senderName;
		if(!empty($eachIntroMailData["sender"])){
			$mailSenderName = $eachIntroMailData["sender"];
		}
		$valueToBeAppend = generateMailData($mailSenderName, getCurrentTime(), parsedownText($eachIntroMailData["content"]));
		array_push($_SESSION["displayData"], $valueToBeAppend);
	}
}

//print_r($_SESSION["displayData"]);
if(!empty($_GET["checkpointId"])){
	$checkpointIdArr = explode(',', $_GET["checkpointId"]);
	foreach($checkpointIdArr as $checkpointId){
		if(isset($directlyMailData[$checkpointId])){
			$currentMailData = $directlyMailData[$checkpointId];
			$currentMailDataContent = "";

			if(in_array(intval($checkpointId), $sequenceArr)){
				$sequenceKey = array_search(intval($checkpointId), $sequenceArr);
				$currentMailDataContent .= $mailPrefixContentSequentially[$sequenceKey]."\n\n";
			}

			$currentMailDataContent .= $currentMailData["content"];
			$currentMailDataContent = parsedownText($currentMailDataContent);
			$shouldAdd = true;
			foreach($_SESSION["displayData"] as $eachValue){
				if($eachValue["content"] == $currentMailDataContent){
					$shouldAdd = false;
				}
			}
			if($shouldAdd == true){
				$mailSenderName = senderName;
				if(!empty($currentMailData["sender"])){
					$mailSenderName = $currentMailData["sender"];
				}
				$valueToBeAppend = generateMailDataWithID($checkpointId, $mailSenderName, getCurrentTime(), $currentMailDataContent);
				array_push($_SESSION["displayData"], $valueToBeAppend);
			}
		}
	}
}

if(!empty($_POST["reply-content"])){
	$replyContent = $_POST["reply-content"];
	$valueToBeAppend = generateMailData(receiverName, getCurrentTime(), $replyContent);
	array_push($_SESSION["displayData"], $valueToBeAppend);
	getMailContentHTML($valueToBeAppend);

	foreach($needReplyMailData as $eachData){
		$canAppendValue = true;
		if($canAppendValue == true){
			if(!empty($eachData["requiredReplyBeforeShown"])){
				if($replyContent == $eachData["requiredReplyBeforeShown"]){
					$canAppendValue = true;
				}else{
					$canAppendValue = false;
				}
			}
		}
		if($canAppendValue == true){
			if(!empty($eachData["requiredCheckpointId"])){
				if(empty($_GET["checkpointId"])){
					$canAppendValue = false;
				}else{
					$checkpointIdArr = explode(',', $_GET["checkpointId"]);
					//echo $eachData["requiredCheckpointId"];
					//print_r($checkpointIdArr);
					if(in_array($eachData["requiredCheckpointId"], $checkpointIdArr)){
						$canAppendValue = true;
					}else{
						$canAppendValue = false;
					}
				}
			}
		}
		if($canAppendValue == true){
			if(!empty($eachData["requiredValidationFunction"])){
				$explodedFunctionArr = explode(",", $eachData["requiredValidationFunction"]);
				if(function_exists($explodedFunctionArr[0])){
					$canAppendValue = call_user_func($explodedFunctionArr[0], $replyContent, array_slice($explodedFunctionArr, 1));
				}
			}
		}
		if($canAppendValue == true){
			$valueToBeAppend = generateMailData(senderName, getCurrentTime(), parsedownText($eachData["content"]));
			array_push($_SESSION["displayData"], $valueToBeAppend);
			getMailContentHTML($valueToBeAppend, true);
		}
	}

	exit;
}

// 建立空$displayData
$displayData = [];
// 將$displayData設定為$_SESSION["displayData"]
$displayData = $_SESSION["displayData"];

if(isset($_GET["showDebug"])){
	//echo "<br /><br /><br />";
	?><pre><?php print_r($_SESSION["displayData"]); ?></pre><?php
	//echo "<br /><br />";
	//print_r($displayData);
}


?>
<!DOCTYPE html>
<html lang="zh-TW">

<!-- 本網頁由何一非制作 -->
<!-- (C) 版權所有 -->
<!-- 多謝您的訪問與支持！ -->

<head>
<meta charset="UTF-8">
<meta name="viewport" content="user-scalable=no" />
<meta name="author" content="何一非" />
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/modal_dialog.css">
<link rel="stylesheet" type="text/css" href="css/loading_animation.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="script.js"></script>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
<title>你的郵箱</title>
</head>
<body>

<div id="circle"></div><div id="circle1"></div>

<?php if(!check($googleFormClosedArrId, $_SESSION["googleFormClosedArr"])){ ?>
<div id="openModal" class="modalDialog">
	<div>
		<div onclick="closePopup()" title="Close" class="close">X</div>
		<iframe src="https://docs.google.com/forms/d/1EyacqUstZ4ka8kiEVCjL7T3mBI9HWJYP3ebkIBvg86Q/viewform?embedded=true<?php if(isset($_GET['teamId'])){ echo '&entry.1936973996='.$_GET['teamId']; } ?>" id="google-form" frameborder="0" marginheight="0" marginwidth="0">載入中...</iframe>
	</div>
</div>
<?php } ?>

<div class="mail-header">
	<span class="sender">寄件人：<span class="email-address"><?php echo $mailData["sender"]; ?></span></span><br />
	<span class="receiver">收件人：<span class="email-address"><?php echo $mailData["receiver"]; ?></span></span>
	<p class="title">標題：<b><?php echo $mailData["title"]; ?></b></p>
</div>

<?php
if(isset($displayData)){
	foreach($displayData as $eachData){
		getMailContentHTML($eachData);
	}
}
?>

<hr style="margin-top: 25px;" id="before-reply-email-content" />

<div class="reply-email-content">
	<form id="reply-email" method="POST">
		<p><label for="reply-content">回覆：</label></p>
		<textarea class="reply" id="reply-content" name="reply-content" required="required"></textarea>
		<input type="submit" value="傳送" />
	</form>
</div>

</body>
</html>