$(window).on("load", function() {
	console.log("window on load");
	$("#circle").fadeOut(500);
	$("#circle1").fadeOut(700);
	scrollToBottom();
});

$(document).ready(function() {
	console.log("document ready");

	openPopup();


	var request;

	// 注意：#reply-email為form id
	$("#reply-email").submit(function(event){

		console.log("已提交 #reply-email form");

		// 有關AJAX代碼的註釋見：http://stackoverflow.com/a/5004276/2603230
		if (request) {
			request.abort();
		}

		var $form = $(this);

		var $replyTextarea = $("textarea#reply-content");
		// 如果textarea內容為空
		if (!$.trim($replyTextarea.val())) {
			alert("郵件回覆不能為空！");
		}else{
			var $inputs = $form.find("input, select, button, textarea");
			var serializedData = $form.serialize();
			$inputs.prop("disabled", true);

			request = $.ajax({
				type: $(this).attr('method') || 'POST',
				url: $(this).attr('action') || window.location.pathname + window.location.search,
				data: serializedData
			});

			request.done(function (response, textStatus, jqXHR){
				console.log("成功獲取AJAX返回結果："+response);
				$replyTextarea.val('');
				$("hr#before-reply-email-content").before(response);
				scrollToBottom();
				//  如果div.should-delay存在
				if($("div.should-delay").length){
					// 延遲兩秒鐘後顯示對方的回覆
					setTimeout(function(){
						$("div.should-delay").show();
						scrollToBottom();
					}, 2000);
				}
			});

			request.fail(function (jqXHR, textStatus, errorThrown){
				console.error("AJAX返回以下錯誤："+textStatus, errorThrown);
			});

			request.always(function () {
				$inputs.prop("disabled", false);
			});
		}

		event.preventDefault();
	});
});

function scrollToBottom() {
	$("html, body").animate({ scrollTop: $(document).height() }, 10);
	//$(document).scrollTop( $(document).height() );
}

function openPopup() {
	$('#openModal').css({'display': 'block'});
	setTimeout(function() {
		$('#openModal').css({'opacity': '1', 'pointer-events': 'auto'});
	}, 500);
}
function closePopup() {
	console.log("已呼叫 closePopup()");

	$('#openModal').css({'opacity': '0', 'pointer-events': 'none'});
	setTimeout(function() {
		$('#openModal').css({'display': 'none'});
	}, 500);


	// AJAX指示PHP要設定已查看Google Form的Session
	var request = $.ajax({
		type: 'POST',
		url: window.location.pathname + window.location.search,
		data: "set-google-form-closed-session=true"
	});

	request.done(function (response, textStatus, jqXHR){
		console.log("成功獲取 closePopup() func 內的AJAX返回結果："+response);
	});

	request.fail(function (jqXHR, textStatus, errorThrown){
		console.error("closePopup() func 內的AJAX返回以下錯誤："+textStatus, errorThrown);
	});

};