(function($){
		Pub.post('/Home/goods/buyLimit',{},function(response){
			if(response.error_code == 0){
        Pub.countDown(response.data.limit_time,"#buy_time");				
				buyList(response.data);
				
			}
		})
		function buyList(data){
			$("#buy_list").empty();
			var html = '';
			if(data.list.length>0){
				for(var i in data.list){					
					html += '<li>'+
						'<div class="img">'+
							'<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id='+data.list[i].id+'"><img src="'+data.list[i].goods_img+'">'+									      
							'</a>'+
						'</div>'+
						'<div class="info-flash">'+
							'<div class="price-show">'+
								'<h4>'+data.list[i].goods_name+'</h4>'+
								'<p><span class="price-goods">团购价:￥'+data.list[i].goods_price+'</span><span class="price-makits">市场价:<del>￥'+data.list[i].market_price+'</del></span></p>'+
							'</div>'+
							'<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id='+data.list[i].id+'" class="rush">立即抢购</a>'+
						'</div>'+
					'</li>';				
				}
				$("#buy_list").append(html);
			}else{
				$("#buy_list").append('<div class="nothing">抱歉，秒杀已经结束！</div>');
			}
		}
})(jQuery)
