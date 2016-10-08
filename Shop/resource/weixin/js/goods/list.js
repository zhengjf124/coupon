
var listData = [];
var search = Pub.searchToObject();
if(search.keyword){
	var keyWord = decodeURI(search.keyword)
}

// 获取数据的参数
var params = {
	cat_id: search.id,
	offset: 0,
	limit: 4,
	order_by: "order_sort-desc",
	keyword: keyWord
};

// 是否还有数据，根据返回的count判断
var active = true;

var getData = function() {
	if(active) {
		Pub.post('/Home/goods/index', params, function(response) {
			if(response.error_code == 0) {
				params.offset += params.limit;
				listing(response.data.list); // 只更新新加的数据                
				if(response.data.count < 5) {
					$("#pullUp").hide();
				} else {
					$("#pullUp").show();
				}
			}
		});
	}
};

$(document).ready(function() {
    $("#txt_search").on("click",toSearch);
    $("#submit_search").on("click",toSearch);
		$("#list").empty();
		//获取列表
		getData();
		//综合排序
		$(".sort1").on("click", function() {
				$("#list").empty();
				$(this).siblings().removeClass("active");
				$(this).siblings().removeClass("active-up");
				$(this).siblings().addClass("acnone");
				$(this).removeClass("acnone");
				$(this).addClass("active");
				params.offset = 0;
				params.order_by = "order_sort-desc";
				getData();
			})
			//销量
		$(".sort2").on("click", function() {
				$("#list").empty();
				$(this).siblings().removeClass("active");
				$(this).siblings().removeClass("active-up");
				$(this).siblings().addClass("acnone");
				if($(this).is(".acnone")) {
					$("#list").empty();
					$(this).removeClass("acnone");
					$(this).addClass("active");
					params.offset = 0;
					params.order_by = "order_sales-desc";
					getData();
				} else if($(this).is(".active")) {
					$("#list").empty();
					$(this).removeClass("active");
					$(this).addClass("active-up");
					$(this).children("i").html("&#xe6d7;");
					params.offset = 0;
					params.order_by = "order_sales-asc";
					getData();
				} else if($(this).is(".active-up")) {
					$("#list").empty();
					$(this).removeClass("active-up");
					$(this).addClass("active");
					$(this).children("i").html("&#xe6d8;");
					params.offset = 0;
					params.order_by = "order_sales-desc";
					getData();
				}
			})
			//价格
		$(".sort3").on("click", function() {
			$("#list").empty();
			$(this).siblings().removeClass("active");
			$(this).siblings().removeClass("active-up");
			$(this).siblings().addClass("acnone");
			if($(this).is(".acnone")) {
				$("#list").empty();
				$(this).removeClass("acnone");
				$(this).addClass("active");
				params.offset = 0;
				params.order_by = "order_price-desc";
				getData();
			} else if($(this).is(".active")) {
				$("#list").empty();
				$(this).removeClass("active");
				$(this).addClass("active-up");
				$(this).children("i").html("&#xe6d7;");
				params.offset = 0;
				params.order_by = "order_price-asc";
				getData();
			} else if($(this).is(".active-up")) {
				$("#list").empty();
				$(this).removeClass("active-up");
				$(this).addClass("active");
				$(this).children("i").html("&#xe6d8;");
				params.offset = 0;
				params.order_by = "order_price-desc";
				getData();
			}
		})



})

function toSearch(){
	var url = Pub.getHtmlUrl('html/search.html');
	location.href=url;
}

function wordClear() {
	var word = $("#word").val();
	if(word != '') {
		$(".clear-word").show();
	} else {
		$(".clear-word").hide();
	}
}

function listing(data) {
	var html = "";
	if(data.length > 0) {
		for(var i in data) {
			html += '<li>' +
				'<a href="' + Pub.getHtmlUrl('html/goods/cont.html') + '?id=' + data[i].id + '">' +
				'<div class="img">' +
				'<span></span><img src="' + data[i].goods_img + '">' +
				'</div>' +
				'<div class="info">' +
				'<div class="name">' + data[i].goods_name + '</div>' +
				'<div class="brief">' +
				'<h5>' + data[i].goods_brief + '</h5>' +
				'</div>' +
				'<div class="tag">';
			if(data[i].is_new == 1) {
				html += '<span class="new">新品</span>';
			}
			if(data[i].is_hot == 1) {
				html += '<span class="hot">热卖</span>';
			}

			html += '</div>' +
				'<div class="price">' +
				'<span>￥' + data[i].goods_price + '</span><del>￥' + data[i].market_price + '</del><em>评论' + data[i].comment_count + '条</em>' +
				'</div>' +
				'</div>' +
				'</a>' +
				'</li>';
		}
		$("#list").append(html);
	} else {
		active = false;
		$("#list").append('<p class="no-list-warning">抱歉，暂无列表！</p>')
	}
	myScroll.refresh();
}
var myScroll,
	pullDownEl, pullDownOffset,
	pullUpEl, pullUpOffset,
	generatedCount = 0;

function pullDownAction() {
	window.location.reload();
}

function pullUpAction() {
	if(active) {
		setTimeout(getData, 1000);
	}
}