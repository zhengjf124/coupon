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
					self.orderList(response.data.list);
					self.initEv();
				}
			});
		},
		orderList: function(data) {
			$("#order_list").empty();
			var html = '';
			if(data && data.length > 0) {
				for(var i in data) {
					if(data[i].order_status == 210) {
						html += '<div class="order-item">' +
							'<div class="order-status">' +
							'<a href="' + Pub.getHtmlUrl("html/user/user-order-cont.html") + '?id=' + data[i].id + '">' +
							'<span>' + data.list[i].order_sn + '</span>' +
							'<strong>待收货</strong>' +
							'</a>' +
							'</div>' +
							'<div class="complete-goods">' +
							'<a href="' + Pub.getHtmlUrl("html/user/user-order-cont.html") + '?id=' + data[i].id + '"><ul>';
						for(var j in data[i].goods) {
							html += '<li>' +
								'<div class="img"><span></span><img src="' + data[i].goods[j].goods_img + '">' +
								'</div>' +
								'<div class="info">' +
								'<div class="name">' +
								'<h4>' + data[i].goods[j].goods_name + '</h4>' +
								'</div>' +
								'<div class="sizes">';
							var str = data[i].goods[j].spec_key_name.split(" ");
							for(var k in str) {
								html += '<h4>' + str[k] + '</h4>';
							}
							html += '</div>' +
								'<div class="price">' +
								'<h4>￥ ' + data[i].goods[j].goods_price + '</h4>' +
								'<h3>X ' + data[i].goods[j].goods_num + '</h3>' +
								'</div>' +
								'</div>' +
								'</li>';
						}
						html += '</ul></a>' +
							'<div class="complete-total">' +
							'<h3 style="text-align: right;">实付款： <span>￥ ' + data[i].order_amount + '</span></h3>' +
							'</div>' +
							'<div class="handle">' +
							'<button class="order-confirm" data-index="'+data[i].id+'">确认收货</button><button>查看物流</button>' +
							'</div>' +
							'</div>' +
							'</div>';
					}
				}
				$("#order_list").append(html);
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
		},
		initEv: function() {
			var _this = this;
			// 确认收货
			$(".order-confirm").on("click", function() {
				var index = $(this).attr("data-index");
				var self = this;
				cloud.asked('您确定收货吗？', function() {
					Pub.post('/Home/order/delivery', {
						id: index
					}, function(response) {
						console.log(response);
						if(response.error_code == 0) {
							cloud.explain('您已成功收货！', 'success', 2000);
							$(self).text('去评价');
							$(self).removeClass("order-confirm");
							$(self).addClass("order-comment");
						}
						// 去评价
						$(".order-comment").on("click", function(e) {
							if(e && e.stopPropagation) {
								e.stopPropagation();
							} else {
								window.event.cancelBubble = true;
							}
							var index = $(this).attr("data-index");
							var url = Pub.getHtmlUrl('html/user/user-judge.html') + '?id=' + index;
							location.href = url;
						});
					})
				}, function() {
					cloud.msg('您选择了取消', '50%', 2000);
				});

			});
			// 去评价
			$(".order-comment").on("click", function(e) {
				if(e && e.stopPropagation) {
					e.stopPropagation();
				} else {
					window.event.cancelBubble = true;
				}
				var index = $(this).attr("data-index");
				var url = 'user-judge.html?id=' + index;
				location.href = url;
			});

		}
		
	}

	// 实例化Page方法
	new Page();
})(jQuery)
