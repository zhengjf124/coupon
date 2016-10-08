(function($) {
	function Page() {
		this.init();
	}
	Page.prototype = {
		init: function() {
			this.initEvent();
			this.getData();
		},
		getData: function() {
			var self = this;
			Pub.post('/Home/order/index', {}, function(response) {
				if(response.error_code == 0) {
					self.orderList(response.data);
				}
			});
		},
		orderList: function(data) {
			$("#order_list").empty();
			var html = '';
			if(data.list.length > 0) {
				for(var i in data.list) {
					if(data.list[i].order_status == 300 || data.list[i].order_status == 310) {
						html += '<div class="order-item">' +
							'<div class="order-status">' +
							'<a href="' + Pub.getHtmlUrl("html/user/user-order-cont.html") + '?id=' + data.list[i].id + '">' +
							'<span>' + data.list[i].order_sn + '</span>';
						if(data.list[i].order_status == 300) {
							html += '<strong>退款中</strong>';
						} else if(data.list[i].order_status == 310) {
							html += '<strong>退款成功</strong>';
						}
						html += '</a>' +
							'</div>' +
							'<div class="complete-goods">' +
							'<a href="' + Pub.getHtmlUrl("html/user/user-order-cont.html") + '?id=' + data.list[i].id + '"><ul>';
						for(var j in data.list[i].goods) {
							html += '<li>' +
								'<div class="img"><span></span><img src="' + data.list[i].goods[j].goods_img + '">' +
								'</div>' +
								'<div class="info">' +
								'<div class="name">' +
								'<h4>' + data.list[i].goods[j].goods_name + '</h4>' +
								'</div>' +
								'<div class="sizes">';
							var str = data.list[i].goods[j].spec_key_name.split(" ");
							for(var k in str) {
								html += '<h4>' + str[k] + '</h4>';
							}
							html += '</div>' +
								'<div class="price">' +
								'<h4>￥ ' + data.list[i].goods[j].goods_price + '</h4>' +
								'<h3>X ' + data.list[i].goods[j].goods_num + '</h3>' +
								'</div>' +
								'</div>' +
								'</li>';
						}
						html += '</ul></a>' +
							'<div class="complete-total">' +
							'<h3 style="text-align: right;">实付款： <span>￥ ' + data.list[i].order_amount + '</span></h3>' +
							'</div>' +
							'<div class="handle">' +
//							'<button>查看详情</button>' +
							'</div>' +
							'</div>' +
							'</div>';
					}
				}
				$("#order_list").append(html);
			} else {
				$("#order_list").append('<div class="nothing">暂无订单！</div>');
			}
		},
		// 相关事件
		initEvent: function() {
			//订单栏目显示
			$("#order_menu").on("click", function() {
				$("#order_menu_show").show();
				$("#order_menu_black").show();
			})
			$("#order_menu_black").on("click", function() {
				$("#order_menu_show").hide();
				$("#order_menu_black").hide();
			})
		}
	}

	// 实例化Page方法
	new Page();
})(jQuery)