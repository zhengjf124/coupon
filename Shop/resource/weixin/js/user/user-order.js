(function($) {
	function Page() {
		this.init();
	}
	Page.prototype = {
		init: function() {
			var _this = this;
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
					html += '<div class="order-item">' +
						'<div class="order-status">' +
						'<a href="' + Pub.getHtmlUrl("html/user/user-order-cont.html") + '?id=' + data[i].id + '">' +
						'<span>' + data[i].order_sn + '</span>';
					if(data[i].order_status == 100) {
						html += '<strong>待付款</strong>';
					} else if(data[i].order_status == 200) {
						html += '<strong>待发货</strong>';
					} else if(data[i].order_status == 101) {
						html += '<strong>已取消</strong>';
					} else if(data[i].order_status == 210) {
						html += '<strong>待收货</strong>';
					} else if(data[i].order_status == 220) {
						html += '<strong>待评价</strong>';
					} else if(data[i].order_status == 222) {
						html += '<strong>交易完成</strong>';
					} else if(data[i].order_status == 300) {
						html += '<strong>退款中</strong>';
					} else if(data[i].order_status == 310) {
						html += '<strong>退款成功</strong>';
					}
					html += '</a>' +
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
						'<div class="handle">';
					if(data[i].order_status == 100) {
						html += '<button class="order-cancel" data-index="' + data[i].id + '">取消订单</button><button class="order-pay" data-index="' + data[i].id + '">立即支付</button>';
					} else if(data[i].order_status == 200) {
						html += '<button class="order-refund" data-index="' + data[i].id + '">申请退款</button>';
					} else if(data[i].order_status == 101) {
						html += '<button class="order-delect" data-index="' + data[i].id + '">删除订单</button>';
					} else if(data[i].order_status == 210) {
						html += '<button class="order-return" data-index="' + data[i].id + '">申请退货</button><button class="order-confirm" data-index="' + data[i].id + '">确认收货</button>';
					} else if(data[i].order_status == 220) {
						html += '<button class="order-return" data-index="' + data[i].id + '">申请退货</button><button class="order-comment" data-index="' + data[i].id + '">去评价</button>';
					}
					html += '</div>' +
						'</div>' +
						'</div>';
				}
				$("#order_list").append(html);
			} else {
				$("#order_list").append('<div class="nothing">暂无订单！</div>');
			}
		},
		// 相关事件
		initEvent: function() {
			var self = this;
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
			var _this = this;
			// 立即支付
			$(".order-pay").on("click", function() {
				var index = $(this).attr("data-index");
				var self = this;
				Pub.post('/Home/Pay/toPay', {
					order_id: index
				}, function(response) {
					console.log(response);
					if(response.error_code == 0) {
						cloud.msg('正在跳转支付！', '50%', 2000);
						_this.payCall(response.data.jsApiParameters);
					} else { //支付接口没调成功，应该跳到订单列表在次支付
						cloud.msg(response.message, '50%', 2000);
					}
				});
			});
			// 删除订单
			$(".order-delect").on("click", function() {
				var index = $(this).attr("data-index");
				var self = this;
				cloud.asked('您确定要删除吗？', function() {
					Pub.post('/Home/order/delete', {
						id: index
					}, function(response) {
						if(response.error_code == 0) {
							cloud.explain('恭喜您，删除成功！', 'success');
							setTimeout($(self).parent().parent().parent().fadeOut(), 2000);
						}
					});
				}, function() {
					cloud.msg('您选择了取消', '50%', 2000);
				});

			});
			// 取消订单
			$(".order-cancel").on("click", function() {
				var index = $(this).attr("data-index");
				var self = this;
				cloud.asked('您确定要取消订单吗', function() {
					Pub.post('/Home/order/cancel', {
					id: index
				}, function(response) {
					if(response.error_code == 0) {
						cloud.explain('订单已取消！', 'success', 1000);
						$(self).text('删除订单');
						$(self).siblings().fadeOut();
						$(self).removeClass('order-cancel');
						$(self).addClass('order-delect');
						$(self).parent().parent().siblings().children('strong').text('已取消');
						$(".order-delect").on("click", function(e) {
							if(e && e.stopPropagation) {
								e.stopPropagation();
							} else {
								window.event.cancelBubble = true;
							}
							var index = $(this).attr("data-index");
							var _self = this;
							Pub.post('/Home/order/delete', {
								id: index
							}, function(response) {
								if(response.error_code == 0) {
									cloud.explain('恭喜您，删除成功！', 'success', 2000);
									setTimeout($(_self).parent().parent().parent().fadeOut(), 2000);
								}
							});
						});
					}
				});
					
				}, function() {
					cloud.msg('您选择了取消', '50%', 2000);
				});
				
			});
			// 申请退款
			$(".order-refund").on("click", function() {
				var index = $(this).attr("data-index");
				var url = Pub.getHtmlUrl('html/user/user-refund.html') + '?id=' + index;
				location.href = url;
			});
			// 申请退货
			$(".order-return").on("click", function() {
				var index = $(this).attr("data-index");
				var url = Pub.getHtmlUrl('html/user/user-return.html') + '?id=' + index;
				location.href = url;
			});
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
				var url = Pub.getHtmlUrl('html/user/user-judge.html') + '?id=' + index;
				location.href = url;
			});

		},
		//微信支付
		apiCall: function(str) {
			var obj = jQuery.parseJSON(str);
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				obj,
				function(res) {
					if(res.err_msg == "get_brand_wcpay_request:ok") {
						cloud.explain('支付成功！！！', 'success', 2000);
						location.href = Pub.getHtmlUrl('html/user/user-order.html');
					} else {
						cloud.explain('支付失败！！！', 2000);
						location.href = Pub.getHtmlUrl('html/user/daiFuKuan.html');
					}
				}
			);
		},
		payCall: function(data) {
			var _this = this;
			if(typeof WeixinJSBridge == "undefined") {
				if(document.addEventListener) {
					document.addEventListener('WeixinJSBridgeReady', _this.apiCall(data), false);
				} else if(document.attachEvent) {
					document.attachEvent('WeixinJSBridgeReady', _this.apiCall(data));
					document.attachEvent('onWeixinJSBridgeReady', _this.apiCall(data));
				}
			} else {
				_this.apiCall(data);
			}
		}

	};
	var page = new Page();
})(jQuery);