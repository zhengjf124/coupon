$(function() {
		
		//关闭顶部悬浮
		$(".close-top").on("click", function() {
				$(".top-susp-h").fadeOut();
				$(this).parent().fadeOut();
			})
			//点击关闭自身
		$(".close-own").on("click", function() {
			$(this).fadeOut();
		})
	})
	//选项卡
function setTab(name, cursel, n) {
	for (i = 1; i <= n; i++) {
		var menu = document.getElementById(name + i);
		var con = document.getElementById("con_" + name + "_" + i);
		menu.className = i == cursel ? "active" : "";
		con.style.display = i == cursel ? "block" : "none";
	}
}