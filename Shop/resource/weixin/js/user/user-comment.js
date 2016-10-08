;
(function($, window, document) {
	function Page() {
		this.init();
	}
	Page.prototype = {
			params: {
				order_id: 0,
				goods_id: 0,
				goods_img: '',
				goods_name: '',
				content: '',
				comment_img: '',
				goods_index: 0,
				speed_index: 0,
				server_index: 0,
				poss_port:'',
				temp:'',
				imgs_file:[]
				
			},
			init: function() {
				this.initParams();
				this.getData();
				this.upLoadImg();
				this.initEvent();
			},
			initParams: function() {
				var search = Pub.searchToObject();
				this.params.order_id = search.id;
				this.params.goods_id = search.gid;
				this.params.goods_img = search.img;
				this.params.goods_name = decodeURI(search.name);
				this.params.poss_port = Pub.getSession("_passport");
			},
			getData: function() {
				$("#goods_info").empty();
				var html = '<div class="img"><a href="../goods/cont.html?id=' + this.params.goods_id + '"><span></span><img src="' + this.params.goods_img + '"></a></div>' +
					'<div class="name">' +
					'<div class="name-center">' +
					'<a href="../goods/cont.html?id=' + this.params.goods_id + '"><h3>' + this.params.goods_name + '</h3></a>' +
					'</div>' +
					'</div>';
				$("#goods_info").append(html);
			},
			initEvent: function() {
				var _self = this;
				// 订单栏目显示
				$("#order_menu").on("click", function() {
					$("#order_menu_show").show();
					$("#order_menu_black").show();
				});
				$("#order_menu_black").on("click", function() {
					$("#order_menu_show").hide();
					$("#order_menu_black").hide();
				});
				//评价星级
				$("#star_goods i").on("click", function() {
					var index_goods = $(this).index();
					$("#star_goods i:lt(" + (index_goods + 1) + ")").addClass("active");
					$("#star_goods i:gt(" + index_goods + ")").removeClass("active");
					_self.params.goods_index = index_goods;
				});
				$("#star_delivery i").on("click", function() {
					var index_delivery = $(this).index();
					$("#star_delivery i:lt(" + (index_delivery + 1) + ")").addClass("active");
					$("#star_delivery i:gt(" + index_delivery + ")").removeClass("active");
					_self.params.speed_index = index_delivery;
				});
				$("#star_service i").on("click", function() {
					var index_service = $(this).index();
					$("#star_service i:lt(" + (index_service + 1) + ")").addClass("active");
					$("#star_service i:gt(" + index_service + ")").removeClass("active");
					_self.params.server_index = index_service;
				});
				$("#text_area").on("change", function() {
						var text_content = $(this).val();
						_self.params.content = text_content;
					})
				//提交数据
				$("#submit_comment").on("click", function() {
					_self.postData()
				});

			},
			postData: function() {
				if(this.params.content !== '' && this.params.goods_index && this.params.speed_index && this.params.server_index) {
					Pub.post('/Home/Comments/add', {
						goods_id: this.params.goods_id,
						order_id: this.params.order_id,
						content: this.params.content,
						comment_img: this.params.imgs_file,
						lv_goods: this.params.goods_index + 1,
						lv_speed: this.params.speed_index + 1,
						lv_server: this.params.server_index + 1,
					}, function(response) {	
						if(response.error_code == 0) {
							console.log(response);
							cloud.msg('评价成功！', '50%', 2000);
						}else if(response.error_code == 90002){
							cloud.msg('您已评价过啦，不能再评了哦！', '50%', 2000);
						}
					})
				} else {
					cloud.msg('请填写完整再提交！', '50%', 2000);
				}
			},
			upLoadImg: function() {
				var _this = this;
				$('#upload').Huploadify({
					auto: true,
					fileTypeExts: '*.jpg;*.png;*.exe;*.jpeg;*.gif',
					multi: true,
					formData: {
						key: 123456,
						_passport: _this.params.poss_port
					},
					fileSizeLimit: 9999,
					fileObjName: 'comment_img_file',
					buttonText: '', //上传按钮上的文字
					itemTemplate: _this.params.temp,
					showUploadedPercent: true, //是否实时显示上传的百分比，如20%
					showUploadedSize: true,
					removeTimeout: 9999999,
					uploader: 'http://gwshop.usrboot.com/Home/Comments/uploadCommentImg',
					onUploadStart: function() {
						//alert('开始上传');
					},
					onInit: function() {
						//alert('初始化');
					},
					'onUploadSuccess': function(file, data, response) {
						//alert('The file ' + file.name + ' was successfully uploaded with a response of ' + response + ':' + data);
						data = jQuery.parseJSON(data);
						console.log(data);						
						var html = '';
						html += '<li><img src="http://gwshop.usrboot.com/' + data.data.imgUrl + '"></li>';
						$("#upload li.add-img").before(html);
						_this.params.imgs_file.push(data.data.imgUrl[0]);
						console.log(_this.params.imgs_file);

					},
					onUploadComplete: function() {
						//alert('上传完成');
					},
					onDelete: function(file) {
						console.log('删除的文件：' + file);
						console.log(file);
					}
				});
			}
		}
	new Page();
})(jQuery, window, document)