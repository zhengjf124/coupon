;(function($,window,document,undefined){
	var Page = function(){
		this.init();
	};
	Page.prototype = {
		init: function(){
			this.getData();
		},
		getData: function(){
			var _this = this;
			Pub.get('/Home/Article/infoList',{},function(response){
				console.log(response);
				_this.listData(response.data.list,_this.initEvent);
			});
		},
		listData: function(data,callback){
			$("#help_list").empty();
			var html = '';
			for(var i = 0; i<data.length; i++){
				html += '<li>'+
						'<a class="help_cont" data-index="'+data[i].id+'">'+
							'<em><i class="green-light">&#xe63e;</i></em>'+
							'<h3><strong>'+data[i].cat_name+'</strong><span><i>&#xe6d5;</i></span></h3>'+
						'</a>'+
					'</li>';
			}
			$("#help_list").append(html);
			callback();
		},
		initEvent: function(){
			$('.help_cont').on('click',function(){
				var index = $(this).attr('data-index');
				var url = Pub.getHtmlUrl('html/help/help-cont.html')+'?id='+index;
				location.href = url;
			});
		}

	};
	var page = new Page();
})(jQuery,window,document);