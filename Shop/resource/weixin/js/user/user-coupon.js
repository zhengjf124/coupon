;
(function($, window, document, undefined) {
	var Page = function() {
		this.init();
	};
	Page.prototype = {
		init: function() {
			this.getData();
			this.getDataUsed();
			this.initEvent();
		},
		getData: function() {
			var _this = this;
			Pub.post('/Home/Coupons/index/', {
				status: 100
			}, function(response) {
				if(response.error_code === 0) {
					_this.couponList(response.data.list);
					$('#count').text(response.data.count)
				}
			});
		},
		getDataUsed: function() {
			Pub.post('/Home/Coupons/index/', {
				status: 300
			}, function(response) {
				if(response.error_code === 0) {
					$('#count_used').text(response.data.count);
				}
			});
		},
		couponList: function(data) {
			$('#coupon_list').empty();
			var html = '';
			if(data && data.length > 0) {
				for(var i in data) {
					html += '<li>' +
						'<div class="img"><span></span><img src="../../images/pic-yhq.jpg"></div>' +
						'<div class="name">' +
						'<div class="name-center">' +
						'<h3>' + data[i].title + '</h3>' +
						'<p>使用期限   ' + data[i].date + '</p>' +
						'</div>' +
						'</div>' +
						'<div class="info">' +
						'<h4>￥<span>' + data[i].money + '</span></h4>' +
						'<h5>满' + data[i].condition + '使用</h5>' +
						'<div class="icon-used"></div>' +
						'<div class="round-left"></div>' +
						'<div class="round-right"></div>' +
						'</div>' +
						'</li>';
				}
				$("#coupon_list").append(html);
			} else {
				$('#coupon_list').css("border", "none");
				$('#coupon_list').append('<div class="nothing">您还没有优惠券！</div>');
			}
		},
		couponListUsed: function(data) {
			$("#coupon_list").empty();
			var html = '';
			if(data && data.length > 0) {
				for(var i in data) {
					html += '<li>' +
						'<div class="img"><span></span><img src="../../images/pic-yhq.jpg"></div>' +
						'<div class="name">' +
						'<div class="name-center">' +
						'<h3>' + data[i].title + '</h3>' +
						'<p>使用期限   ' + data[i].date + '</p>' +
						'</div>' +
						'</div>' +
						'<div class="info used">' +
						'<h4>￥<span>' + data[i].money + '</span></h4>' +
						'<h5>满' + data[i].condition + '使用</h5>' +
						'<div class="icon-used"></div>' +
						'<div class="round-left"></div>' +
						'<div class="round-right"></div>' +
						'</div>' +
						'</li>';
				}
				$("#coupon_list").append(html);
			} else {
				$('#coupon_list').css("border", "none");
				$("#coupon_list").append('<div class="nothing">您还没有优惠券过期！</div>');
			}
		},
		initEvent: function() {
			var _this = this;
			$('#coupon').on("click", function() {
				$(this).siblings().removeClass('active');
				$(this).addClass('active');
				_this.getData();
			});
			$('#coupon_used').on("click", function() {
				$(this).siblings().removeClass('active');
				$(this).addClass('active');
				Pub.post('/Home/Coupons/index/', {
					status: 300
				}, function(response) {
					if(response.error_code === 0) {
						_this.couponListUsed(response.data.list);
					}
				});
			})
		}
	};
	var page = new Page();
})(jQuery, window, document)