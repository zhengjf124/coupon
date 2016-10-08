//加入购物车/购买
function buy_show() {
	$("#buy-black").fadeIn();
	$("#buy-light").slideDown();
}

function buy_hide() {
	$("#buy-light").slideUp();
	$("#buy-black").fadeOut();
}
//搜索
function search_val() {
	var word = $("#word").val();
	$("#word").attr('value', word);
	if (word != '') {
		$(".clear-word").show();
	} else {
		$(".clear-word").hide();
	}
}
//评论
function star(n) {
	var array = new Array();
	array[0] = document.getElementById("oneStar");
	array[1] = document.getElementById("twoStar");
	array[2] = document.getElementById("threeStar");
	array[3] = document.getElementById("fourStar");
	array[4] = document.getElementById("fiveStar");
	for (var i = 0; i <= n; i++) {
		array[i].className = "active";
	}
	for (var j = 4; j > n; j--) {
		array[j].className = "";
	}
	document.getElementById("evaluate").innerText = "" + (n + 1) + "星";
}

function star1(m) {
	var array1 = new Array();
	array1[0] = document.getElementById("oneStar1");
	array1[1] = document.getElementById("twoStar1");
	array1[2] = document.getElementById("threeStar1");
	array1[3] = document.getElementById("fourStar1");
	array1[4] = document.getElementById("fiveStar1");
	for (var i1 = 0; i1 <= m; i1++) {
		array1[i1].className = "active";
	}
	for (var j1 = 4; j1 > m; j1--) {
		array1[j1].className = "";
	}
	document.getElementById("evaluate1").innerText = "" + (m + 1) + "星";
}

function star2(t) {
	var array2 = new Array();
	array2[0] = document.getElementById("oneStar2");
	array2[1] = document.getElementById("twoStar2");
	array2[2] = document.getElementById("threeStar2");
	array2[3] = document.getElementById("fourStar2");
	array2[4] = document.getElementById("fiveStar2");
	for (var i2 = 0; i2 <= t; i2++) {
		array2[i2].className = "active";
	}
	for (var j2 = 4; j2 > t; j2--) {
		array2[j2].className = "";
	}
	document.getElementById("evaluate2").innerText = "" + (t + 1) + "星";
}

$(function() {
	//搜索清除
	$(".clear-word").on("click", function() {
			$("#word").val('');
			$(".clear-word").hide();
		})
		//商品筛选
	$(".sort1").on("click",function(){
		if($(this).is(".active")){
			$(this).removeClass("active");
			$(this).children("i").html("&#xe6d8;");
		}else{			
			$(this).addClass("active");
			$(this).children("i").html("&#xe6d7;");
		}
	})
	$(".sort2").on("click",function(){
		if($(this).is(".active")){
			$(this).removeClass("active");
			$(this).children("i").html("&#xe6d8;");
		}else{			
			$(this).addClass("active");
			$(this).children("i").html("&#xe6d7;");
		}
	})
	$(".sort3").on("click",function(){
		if($(this).is(".active")){
			$(this).removeClass("active");
			$(this).children("i").html("&#xe6d8;");
		}else{			
			$(this).addClass("active");
			$(this).children("i").html("&#xe6d7;");
		}
	})
	$(".sort4").on("click", function() {
		$("html").css("overflow-y", "hidden");
		$("#black-screen").fadeIn(300);
		$("#dialog-screen").fadeIn(500);
	})
	$(".screen-close").on("click", function() {
		$("#black-screen").fadeOut(300);
		$("#dialog-screen").fadeOut(500);
		$("html").css("overflow-y", "auto");
	})
	$(".screen-content dl dd label").on("click", function() {
		$(this).siblings().removeClass("active");
		$(this).addClass("active");
	})

	//优惠券选择
	$("#coupon").on("click",function(){
		$("#coupon-black").fadeIn();
		$("#coupon-light").slideDown();
	})
	$("#coupon-black").on("click",function(){
		$("#coupon-light").slideUp();
		$("#coupon-black").fadeOut();
	})
	$(".coupon-show ul li").on("click",function(){
		$(this).siblings().children("i").html("&#xe61a;");
		$(this).siblings().removeClass("active");		
		$(this).children("i").html("&#xe619;");
		$(this).addClass("active");
	})
		//发票选择
	$("#bill").on("click", function() {
		$("#bill-show").toggle(300);
	})
	$("#bill-show label").on("click", function() {
		$(this).siblings().removeClass("active");
		$(this).addClass("active");
	})
	$("#bill-show label:first-child").on("click", function() {
		$("#bill-form").hide();
	})
	$("#bill-show label:nth-child(2)").on("click", function() {
		$("#bill-form").hide();
	})
	$("#bill-show label:last-child").on("click", function() {
			$("#bill-form").show(300);
		})
	
		//购物车编辑
	$(".cart-edit").toggle(
		function() {
			$(this).html("完成");
			$(".edit-sizes").show();
			$(".edit-container").show();
		},
		function() {
			$(this).html("编辑全部");
			$(".edit-sizes").hide();
			$(".edit-container").hide();
		}
	)
	$(".edit").toggle(
		function() {
			$(this).html("完成");
			$(this).siblings(".edit-sizes").show();
			$(this).siblings(".edit-container").show();
		},
		function() {
			$(this).html("编辑");
			$(this).siblings(".edit-sizes").hide();
			$(this).siblings(".edit-container").hide();
		}
	)
	$(".edit-sizes").on("click", function() {
			$("#buy-black").fadeIn();
			$("#buy-light").slideDown();
		})
		//订单栏目
	$("#order-menu").toggle(function() {
		$(".order-menu-black").fadeIn();
		$(".order-menu").slideDown();
	}, function() {
		$(".order-menu").slideUp();
		$(".order-menu-black").fadeOut();
	})
	$(".order-menu-black").on("click", function() {
			$(".order-menu").slideUp();
			$(".order-menu-black").fadeOut();
		})
		//收藏夹取消收藏
	$(".favorites-cancel").on("click", function() {
		if (confirm("是否取消收藏")) {
			$(this).parent().hide();
		}

	})
	//分销列表
	$(".dis-share").toggle(function(){
		$(".share-show").hide();
		$(this).siblings(".share-show").show();
	},function(){
		$(".share-show").hide();
	})
	

})