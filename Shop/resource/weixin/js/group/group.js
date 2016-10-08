(function($){
	Pub.post('/Home/goods/buyGroup',{},function(response){
		if(response.error_code == 0){
			groupList(response.data);
			var showHeight = $(".price-show").height();
			$(".img a").css("min-height",showHeight);
			for(var i in response.data.list){
				Pub.countDown(response.data.list[i].end_time,"#times_"+i);
			}
			
		}
	})
	function groupList(data){
		$("#group_list").empty();
		var html = '';
		if(data.list.length>0){
			for(var i in data.list){
				html += '<li>'+
						'<div class="img">'+
							'<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id='+data.list[i].id+'"><img src="'+data.list[i].goods_img+'" class="img-response">'+
							'<div class="price-show">'+
								'<h4>'+data.list[i].goods_name+'</h4>'+
								'<p><span class="price-goods">团购价：￥'+data.list[i].goods_price+'</span><span class="price-makits">市场价：<del>￥'+data.list[i].market_price+'</del></span></p>'+
							'</div>'+				      
							'</a>'+
						'</div>'+
						'<div class="info">'+
							'<div class="time">'+
								'<h5>距结束时间</h5>'+
								'<div class="time-container" id="times_'+i+'">'+
									'<span class="day">0</span>天<span class class="hour">00</span>:<span class="minute">00</span>:<span class="second">00</span><label>125人参团</label>'+
								'</div>'+
							'</div>'+
							'<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id='+data.list[i].id+'" class="rush">立即抢购</a>'+
						'</div>'+
					'</li>';			
			}
			$("#group_list").append(html);
		}else{
			$("#group_list").append('<div class="nothing">暂无团购列表！</div>');
		}
	}
	
})(jQuery)
