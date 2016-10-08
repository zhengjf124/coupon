(function($) {
	function Page() {
		this.init();
	}
	Page.prototype = {
		init: function() {
			this.getData();
		},
		getData: function() {
			var self = this,
				search = Pub.searchToObject();
			Pub.post('/Home/order/detail', {
				id: search.id
			}, function(response) {
				if(response.error_code == 0) {
					console.log(response.data);
					self.orderStatus(response.data.order_status);
					self.orderLogistics(response.data.logistics_tracking);
					self.toOrderLogistics(response.data);
					self.orderAddress(response.data.user_address);
					self.orderInvoice(response.data);
					self.orderGoods(response.data.goods);
					self.orderHandle(response.data);
					self.orderLike(response.data.goods_like);
					self.orderCount(response.data);
					self.orderTotal(response.data);
					self.orderTime(response.data);
					self.initEv();
				}
			});
		},
		// 订单状态
		orderStatus: function(data) {
			$("#order_status").empty();
			var html = '';
			html += '<div class="font">' +
				'<div class="font-center">';
			if(data == 100) {
				html += '待付款';
			} else if(data == 200) {
				html += '待发货';
			} else if(data == 101) {
				html += '已取消';
			} else if(data == 210) {
				html += '待收货';
			} else if(data == 220) {
				html += '待评价';
			} else if(data == 222) {
				html += '交易完成';
			} else if(data == 300) {
				html += '退款中';
			} else if(data == 310) {
				html += '退款成功';
			}
			html += '</div>' +
				'</div>' +
				'<div class="img">' +
				'<span></span><img src="../../images//img-pay.png">' +
				'</div>';
			$("#order_status").append(html);
		},
		// 物流信息
		orderLogistics: function(data) {
			$("#order_logistics").empty();
			if(data.length > 0) {
				var html = '';
				html += '<h4>' + data[0].context + '</h4>' +
					'<h5>' + data[0].ftime + '</h5>';
				$("#order_logistics").append(html);
			} else {
				$("#order_logistics").append('<h5>暂无物流信息！</h5>');
			}

		},
		toOrderLogistics: function(data) {
			var url = Pub.getHtmlUrl("html/user/user-order-cont-logistics.html") + '?id=' + data.id;
			$("#to_order_logistics").on("click", function() {
				location.href = url;
			})
		},
		// 订单收货地址
		orderAddress: function(data) {
			$("#order_address").empty();
			var mobile = data.mobile;
			var lmobile = mobile.substr(3, 4);
			var mymobile = mobile.replace(lmobile, "****");
			var html = '';
			html += '<em>&#xe6e2;</em>' +
				'<h4>收件人：' + data.consignee + '&nbsp;&nbsp;' + mymobile + '</h4>' +
				'<p>收货地址：' + data.province + data.city + data.district + data.address + '</p>';
			$("#order_address").append(html);
		},
		// 发票信息
		orderInvoice: function(data) {
			$("#order_invoice").empty();
			var html = '';
			html += '<h4>发票信息</h4>' +
				'<h4>发票抬头 ：' + data.inv_payee + ' </h4>';
			$("#order_invoice").append(html);
		},
		// 订单商品
		orderGoods: function(data) {
			$("#order_goods").empty();
			var html = '';
			if(data) {
				for(var i in data) {
					html += '<li>' +
						'<div class="img"><a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id=' + data[i].goods_id + '"><span></span><img src="' + data[i].goods_img + '"></a></div>' +
						'<div class="info">' +
						'<div class="name">' +
						'<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id=' + data[i].goods_id + '"><h4>' + data[i].goods_name + '</a>' +
						'</div>' +
						'<div class="sizes">';
					var str = data[i].spec_key_name;
					var strs = str.split(' ');
					for(var j in strs) {
						html += '<h4>' + strs[j] + '</h4>';
					}
					html += '</div>' +
						'<div class="price">' +
						'<h4>￥ ' + data[i].goods_price + '</h4>' +
						'<h3>X ' + data[i].goods_num + '</h3>' +
						'</div>' +
						'</div>' +
						'</li>';
				}
				$("#order_goods").append(html);
			}
		},
		// 订单操作
		orderHandle: function(data) {
			$("#order_handle").empty();
			var html = '';
			if(data.order_status == 100) {
				html += '<button class="order-cancel" data-index="' + data.id + '">取消订单</button><button class="order-pay" data-index="' + data.id + '">立即支付</button>';
			} else if(data.order_status == 200) {
				html += '<button class="order-refund" data-index="' + data.id + '">申请退款</button>';
			} else if(data.order_status == 101) {
				html += '<button class="order-delect" data-index="' + data.id + '">删除订单</button>';
			} else if(data.order_status == 210) {
				html += '<button class="order-return" data-index="' + data.id + '">申请退货</button><button class="order-confirm" data-index="' + data.id + '">确认收货</button>';
			} else if(data.order_status == 220) {
				html += '<button class="order-return" data-index="' + data.id + '">申请退货</button><button class="order-comment" data-index="' + data.id + '">去评价</button>';
			}
			$("#order_handle").append(html);
		},
		// 订单计算
		orderCount: function(data) {
			$("#order_count").empty();
			var html = '';
			html += '<dl>' +
				'<dt>商品金额：</dt><dd>￥' + data.goods_amount + '</dd>' +
				'</dl>' +
				'<dl>' +
				'<dt>运费：</dt><dd>+￥' + data.shipping_fee + '</dd>' +
				'</dl>' +
				'<dl>' +
				'<dt>优惠劵：</dt><dd>-￥' + data.coupon_price + '</dd>' +
				'</dl>' +
				'<dl>' +
				'<dt>积分抵现：</dt><dd>-￥' + data.credit_money + '</dd>' +
				'</dl>';
			$("#order_count").append(html);
		},
		// 订单总额
		orderTotal: function(data) {
			$("#order_total").empty();
			var html = '';
			html += '<h3>实付款 <span>￥' + data.order_amount + '</span></h3>';
			$("#order_total").append(html);
		},
		// 订单时间
		orderTime: function(data) {
			$("#order_time").empty();
			var html = '';
			html += '<h5>订单编号：  ' + data.order_sn + '</h5>' +
				'<h5>下单时间：  ' + data.add_time + '</h5>';
			$("#order_time").append(html);
		},
		// 猜你喜欢
		orderLike: function(data) {
			$("#goods_like").empty();
			var html = '';
			if(data.length > 0) {
				html += '<div class="hot-sale-title">' +
					'<i>&#xe634;</i><strong>猜你喜欢</strong>' +
					'</div>' +
					'<div class="list-sm">' +
					'<ul>';
				for(var i in data) {
					html += '<li>' +
						'<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id=' + data[i].id + '">' +
						'<div class="img">' +
						'<span></span><img src="' + data[i].original_img + '">' +
						'</div>' +
						'<div class="info">' +
						'<div class="name">' +
						'<h4>' + data[i].goods_name + '</h4>' +
						'</div>' +
						'<div class="price">' +
						'<h4>￥' + data[i].goods_price + '</h4>' +
						'</div>' +
						'</div>' +
						'</a>' +
						'</li>';
				}
				html += '</ul>' +
					'</div>';
				$("#goods_like").append(html);
			}
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
					} else {
						cloud.explain('支付失败！！！', 2000);
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
	// 实例化Page
	new Page();
})(jQuery)