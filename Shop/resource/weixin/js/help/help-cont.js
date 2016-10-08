;(function($,window,document,undefined){
	var Page = function(){
		this.init();
	};
	Page.prototype = {
		params:{
			id: 0
		},
		init: function(){
			this.initParams();
			this.getData();
		},
		initParams: function(){
			var search = Pub.searchToObject();
			this.params.id = search.id;
		},
		getData: function(){
			var _this = this;
			Pub.post('/Home/Article/detail/',{id: _this.params.id},function(response){
				_this.contData(response.data.content);
			});
		},
		contData: function(data){
			$('#help_cont').empty();
			var html = data.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"').replace(/&amp;nbsp;/g,' ');
			console.log(html);
			$('#help_cont').append(html);
		}

	};	
	var page = new Page();
})(jQuery,window,document)