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
				}
			});
		},
		//获取全部物流信息
		orderStatus: function(data) {
			$("#order_status").empty();
			var html = '';
			html += '<div class="font">' +
				'<div class="font-center"><strong>订单跟踪</strong><br/>';
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
			}
			html += '</div>' +
				'</div>' +
				'<div class="img">' +
				'<span></span><img src="../../images//img-pay.png">' +
				'</div>';
			$("#order_status").append(html);
		},
		//物流信息
		orderLogistics: function(data) {
			$("#order_logistics_all").empty();
			var html = '';
			for(var i in data){
				html +='<div class="item">'+
							'<h4>'+data[i].context+'</h4>'+
							'<h5>'+data[i].ftime+'</h5>'+
					  '</div>';
			}
			$("#order_logistics_all").append(html);
		}
		
	};
	//实例化Page
	new Page();
})(jQuery)