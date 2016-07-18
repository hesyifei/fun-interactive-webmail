// 當網頁（包括圖片）以加載完成
$(window).on("load", function() {
	console.log("window on load");

	// 隱藏載入中特效
	$("#circle").fadeOut(500);
	$("#circle1").fadeOut(700);

	// 移至頁面底部
	scrollToBottom();
});

// 當網頁加載完成
$(document).ready(function() {
	console.log("document ready");

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

			// window.location.pathname + window.location.search 以包括GET內容
			request = $.ajax({
				type: $(this).attr('method') || 'POST',
				url: $(this).attr('action') || window.location.pathname + window.location.search,
				data: serializedData
			});

			request.done(function (response, textStatus, jqXHR){
				console.log("AJAX成功獲取PHP返回結果："+response);
				// 清空回覆框內容
				$replyTextarea.val('');
				// 將PHP所返回的內容插入於評論框前的<hr />前
				$("hr#before-reply-email-content").before(response);
				// 移至頁面底部
				scrollToBottom();
				// 如果div.should-delay存在
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
	// 詳見：http://stackoverflow.com/a/4249365/2603230
	$("html, body").animate({ scrollTop: $(document).height() }, 10);
}
