
var password = '';
$('#cartList').html('');
$('#selectNum').html(0);

$(document).ready(function() {
	

	/*
	//判断是否已经缓存登录
	if(localStorage.password == undefined){
		password = GetLogin();
	}else{
		password = localStorage.getItem("password"); 
		//alert(password);	
	}
	//登录模块
	function GetLogin(){
		Pub.post(website+'/Home/Login/login', {user_name:'lihoqi',password:'3593256'}, function(response){
			if (response.error_code == 0) {
				//console.log(response);					
				localStorage.setItem("password",response.data.passport);
				return response.data.passport;
				//var value = localStorage.getItem("password"); 
				//alert(value);
			}
		});	
	}
	*/
	
	GetCart();//获取购物车列表
	
	
	var cart = new $.shopCart({		
		shopCartBoxId:'cartList',//订单列表根目录ID
		TotalAmountBoxId:'TotalAmount',//用于存放计算总金额的ID
	});
	
	cart.starShoppingCart();
	
	
	//显示所有编辑
	$('.cart-edit').click(function(){
		var dataShowAll = $(this).attr('dataShow');
		if(dataShowAll == 1){
			$('#cartList').find('.editBox').each(function(){
				$(this).hide();	
			});
			
			var goods = new Array();
			var goodsKey = 0;
			
			$('#cartList li').each(function(){
								
				goods[goodsKey] = {};
				
				var buyNum = $(this).find('.Input_CartListNum').val();
				var carId = $(this).find('input[cartlevel=2]').val();	
				
				goods[goodsKey] = {cart_id:carId,goods_num:buyNum};		
				goodsKey++ ;				
			});
			
			//console.log(goods);
			changeCartNum(goods);//更新商品数量			
			
			$(this).attr('dataShow',0);
			$(this).text('编辑全部');
		}else{
			$('#cartList').find('.editBox').each(function(){
				$(this).show();	
			});
			$(this).attr('dataShow',1);
			$(this).text('完成编辑');
		}
		
	});
	
	//编辑显示
	$('#cartList').delegate(".edit","click",function(){
		
		var dataShowAll = $('.cart-edit').attr('dataShow');
		if(dataShowAll == 1){//如果编辑全部被单击就不能用单个编辑
			return;
		}	
		
		$(this).parent().find('.editBox').show();
		$(this).html('完成');
		$(this).addClass('edit_ok');
		
		//$('.editBox').show();
	})
	
	
	//编辑完成
	$('#cartList').delegate(".edit_ok","click",function(){
		$(this).html('编辑');
		$(this).removeClass('edit_ok');
		var goods = new Array();
		var buyNum = $(this).parents('li').find('.Input_CartListNum').val();
		var carId = $(this).parents('li').find('input[cartlevel=2]').val();
		
		goods[0] = {};
		
		goods[0] = {cart_id:carId,goods_num:buyNum};
		
		//console.log(goods);
		changeCartNum(goods);//更新商品数量
		
			
		$(this).parents('li').find('.editBox').hide();
		
		//$('.editBox').show();
	})
	
	
	
	//更新购物车商品数量
	function changeCartNum(goods){
		Pub.post('/Home/Cart/editGoods', {/*_passport:password,*/goods:goods}, function(response) {
			if (response.error_code == 0) {
				//console.log(response);
				webToast("恭喜您，修改成功","middle",2000);
			}else{
				webToast(response.message,"middle",2000);
			}
			//console.log(response);
		});	
	}
	
	//获取购物车商品信息
	function GetCart(){
		Pub.post('/Home/Cart/index', {/*_passport:password*/}, function(response) {
			if (response.error_code == 0) {
				console.log(response);
				
				$('#cartList').html('');
				var cartHtml = '';
				
				$.each(response.data.list, function(key, value) {
			
					cartHtml += '<li>'+
								'<div class="check">'+
									'<label><input name="" type="checkbox" value="'+value.id+'" cartlevel="2" class="check"></label>'+
								'</div>'+
								'<div class="img">'+
									'<span></span><img src="'+value.goods_img+'">'+//图片
								'</div>'+
								'<div class="info">'+
										'<a class="edit">编辑</a>'+
										'<div class="name">'+
											'<h3>'+value.goods_name+'</h3>'+//名称
										'</div>'+
										'<div class="sizes">'+
											'<p>'+value.spec_key_name+'</p>'+//规格属性
											//'<p>规格：1920*1200</p>'+
										'</div>'+
										'<div class="CartShopPrice" style="display:none">'+value.goods_price+'</div>'+//单价
										'<div class="price"><span>￥ <span class="CartListTotalAmount">'+value.goods_price+'</span><del>￥'+value.market_price+'</del></span><em>X <i class="goods_num">'+value.goods_num+'</i></em></div>'+
										'<!--编辑-->'+
										'<div class="editBox" style="display:none">'+
											'<div class="edit-sizes">'+
												'<span><label>属性：白色</label><label>规格：1920*1200</label></span><i>&#xe6d8;</i>'+
											'</div>'+
											'<div class="edit-container">'+
												'<div class="qty">'+
													'<button class="bt_CartReduce"><i>&minus;</i></button><input class="Input_CartListNum" type="text" value="'+value.goods_num+'" /><button class="bt_CartPlus"><i>&#43;</i></button>'+
												'</div>'+
												'<a class="delect delCartList">删除</a>'+
											'</div>'+
										'</div>'+
										'<!--编辑:over-->'+
									'</div>'+
								'</li>'
					
				});	//	end each
				
				$('#cartList').append(cartHtml);
				
			}
		});	
	}
	
	/*
	//添加到购物车
	addCart();
	function addCart(){
		Pub.post('http://gwshop.usrboot.com/Home/Cart/add', {goods_id:5,goods_num:1,_passport:password,goods_spec:{'内存':1,'颜色':3}}, function(response) {
			if (response.error_code == 0) {
				console.log(response);
			}
		});
	}*/
	
	
	
	
	
})
