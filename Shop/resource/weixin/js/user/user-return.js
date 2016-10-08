;
(function($, window, document, undefined) {
	function Page() {
		this.init();
	};
	Page.prototype = {
		params: {
			order_id: 0,
			goods_id: 0,
			order_sn: 0,
			reason: '',
			type: 0
		},
		init: function() {
			this.initParams();
			this.getData();
		},
		initParams: function() {
			var search = Pub.searchToObject();
			this.params.order_id = search.id;
		},
		getData: function() {
			var _this = this;
			Pub.post('/Home/order/detail', {
				id: _this.params.order_id
			}, function(response) {
				if(response.error_code == 0){
					_this.order_sn = response.data.order_sn;
					_this.refundGoods(response.data.goods);
					_this.initEvent();
				}
			});
		},
		refundGoods: function(data){
			$("#refund_goods").empty();
			var html = '';
			for(var i in data){
				html += '<li>'+
								'<div class="check" data-index="'+data[i].goods_id+'"><label><i>&#xe619;</i></label></div>'+
								'<div class="img"><a href="'+Pub.getHtmlUrl("html/goods/cont.html")+'?id='+data[i].goods_id+'"><span></span><img src="'+data[i].goods_img+'"></a>'+
								'</div>'+
								'<div class="info-refund">'+
									'<div class="name">'+
										'<a href="'+Pub.getHtmlUrl("html/goods/cont.html")+'?id='+data[i].goods_id+'"><h3>'+data[i].goods_name+'</h3></a>'+
									'</div>'+
									'<div class="sizes">';
									var str = data[i].spec_key_name.split(' ');
									for(var s in str){
										html += '<h4>'+str[s]+'</h4>';
									}
								html += '</div>'+
									'<div class="price">'+
										'<h4>￥ '+data[i].goods_price+'</h4>'+
										'<h3>X '+data[i].goods_num+'</h3>'+
									'</div>'+
								'</div>'+
							'</li>';
			}
			$("#refund_goods").append(html);
		},
		initEvent: function(){
			var _this = this;
			$(".check").on("click",function(){
				$(".check").find("label").removeClass("active");
				$(this).find("label").addClass("active");
				var index = $(this).attr("data-index");
				_this.params.goods_id = index;
			});
			$("#refund_text").on("change",function(){
				var refund_cont = $(this).val();
				_this.params.reason = refund_cont;
			});
			$("#submit_return").on("click",function(){
				_this.postData();
			})
		},
		postData: function(){
			var _this = this;
			if(_this.params.goods_id ==''){
				cloud.msg('请选择商品！','50%',1500);
			}else if(_this.params.reason == ''){
				cloud.msg('请填写退货原因！','50%',1500);
			}else{
				Pub.post('/Home/order/applyServer',{
					order_id: _this.params.order_id,
					goods_id: _this.params.goods_id,
					order_sn: _this.params.order_sn,
					reason: _this.params.reason,
					type: 0
				},function(response){
					if(response.error_code === 0){
						cloud.msg('你已成功申请退货,请等候我们处理！','50%',1500);
					}else if(response.error_code === 20002){
						cloud.msg('你已申请过退货,请耐心等候我们处理！','50%',1500)
					}
				});
			}
			
		}

	};
	var page = new Page();
})(jQuery, window, document)