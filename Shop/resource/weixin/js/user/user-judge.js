;
(function($, window, document, undefined) {
	var Page = function() {
		this.init();
	}
	Page.prototype = {
		init: function() {
			this.getData();
			this.initEvent();
		},
		getData: function() {
			var _this = this;
			var search = Pub.searchToObject();
			Pub.post('/Home/order/detail', {
				id: search.id
			}, function(response) {
				if(response.error_code == 0) {
					console.log(response);
					_this.orderStatus(response.data);
					_this.orderGoods(response.data.goods);
					_this.initEv();
				}
			});
		},
		orderStatus: function(data) {
			$("#order_status").empty();
			var html = '';
			html += '<span>' + data.order_sn + '</span>';
			if(data.order_status == 100) {
				html += '<strong>待付款</strong>';
			} else if(data.order_status == 200) {
				html += '<strong>待发货</strong>';
			} else if(data.order_status == 101) {
				html += '<strong>已取消</strong>';
			} else if(data.order_status == 210) {
				html += '<strong>待收货</strong>';
			} else if(data.order_status == 220) {
				html += '<strong>待评价</strong>';
			} else if(data.order_status == 222) {
				html += '<strong>交易完成</strong>';
			}
			$("#order_status").append(html);
		},
		orderGoods: function(data) {
			$("#comment_goods").empty();
			var html = '';
			for(var i in data) {
				html += '<li>' +
					'<div class="img">' +
					'<a href="../goods/cont.html?id=' + data[i].goods_id + '"><span></span><img src="' + data[i].goods_img + '"></a>' +
					'</div>' +
					'<div class="info">' +
					'<div class="name">' +
					'<a href="../goods/cont.html?id=' + data[i].goods_id + '"><h3>' + data[i].goods_name + '</h3></a>' +
					'</div>' +
					'<div class="sizes">';
				var str = data[i].spec_key_name;
				var strs = str.split(' ');
				for(var s in strs) {
					html += '<h4>' + strs[s] + '</h4>';
				}
				html += '</div>' +
					'<div class="price">' +
					'<h4>￥ ' + data[i].goods_price + '</h4>' +
					'<h3>X ' + data[i].goods_num + '</h3>' +
					'</div>' +
					'</div>' +
					'<div class="handle" style="width:100%; padding:.1rem 0;">' +
					'<button class="btn-comment" data-index="' + data[i].goods_id + '" data-img="' + data[i].goods_img + '" data-name=' + data[i].goods_name + '>发表评论</button>' +
					'</div>' +
					'</li>';
			}
			$("#comment_goods").append(html);
		},
		initEvent: function() {
			//订单栏目显示
			$("#order_menu").on("click", function() {
				$("#order_menu_show").show();
				$("#order_menu_black").show();
			});
			$("#order_menu_black").on("click", function() {
				$("#order_menu_show").hide();
				$("#order_menu_black").hide();
			});
		},
		initEv: function() {
			var search = Pub.searchToObject();
			$(".btn-comment").on("click", function() {
				var index = $(this).attr("data-index");
				var img = $(this).attr("data-img");
				var name = encodeURI($(this).attr("data-name"));
				var url = 'user-comment.html?id=' + search.id + '&gid=' + index + '&img=' + img + '&name=' + name;
				location.href = url;
			})
		}
	}
	new Page();
})(jQuery, window, document)