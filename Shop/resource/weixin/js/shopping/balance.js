
var password = '';
var jsApiParameters = '';
$('#cartList').html('');
var adDetailed = {};

//调用微信JS api 支付
function jsApiCall()
{
	//var str = $("#parameters").val();
	var str = jsApiParameters;
	var obj = jQuery.parseJSON(str);
	WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			obj,
			function(res){
			//WeixinJSBridge.log(res.err_msg);
			// alert(res.err_code);
			//alert(res.err_desc);
			// alert(res.err_msg);
			if(res.err_msg == "get_brand_wcpay_request:ok"){
				alert('支付成功！！！');
				location.href = Pub.getHtmlUrl('html/user/user-order.html');
			}else{
				alert('支付失败');
				//history.back(-1);
				location.href = Pub.getHtmlUrl('html/user/daiFuKuan.html');
			}
		}
);
}

function callpay()
{

	if (typeof WeixinJSBridge == "undefined"){

		if( document.addEventListener ){

			document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		}else if (document.attachEvent){

			document.attachEvent('WeixinJSBridgeReady', jsApiCall);
			document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		}
	}else{
		//alert('ok3');
		jsApiCall();
	}

}




$(document).ready(function() {
	
	/*//模拟登录
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
	}*/
	
	//购物车选中的商品ID
	var CartIdArray = JSON.parse(localStorage.getItem("CartIdArray"));
	console.log(CartIdArray);
	
	//alert(CartIdArray[0]);
	
	OrderConf();
	
	
	
	//获取购物车商品信息
	function OrderConf(){
					
		Pub.post('/Home/order/confirm', {cart_id:CartIdArray/*, _passport:password*/}, function(response) {
			if (response.error_code == 0) {
				
				console.log(response);
				console.log(localStorage.addressId);	
				//地址生成
				if(localStorage.addressId == undefined){					
					//生成收货地址
					addressHtml(response.data.address);
				}else{
					var addressId = localStorage.getItem('addressId');
					
					Pub.post('/Home/UserAddress/detail', {id:addressId/*, _passport:password*/}, function(addressDetailed) {
						if (addressDetailed.error_code == 0) {	
							//console.log(addressDetailed.data);	
							//生成收货地址
							addressHtml(addressDetailed.data);						
								
						}						
					});	
													
				}
				
				//商品列表
				$('#cartList').html('');
				var cartHtml = '';
				$.each(response.data.goods, function(key, value) {	
					cartHtml += '<li>'+
									'<a href="id='+value.goods_id+'">'+
										'<div class="cartId" dataId="'+value.id+'"></div>'+
										'<div class="img"><span></span><img src="'+value.goods_img+'"></div>'+//图片
										'<div class="info">'+
											'<div class="name"><h3>'+value.goods_name+'</h3></div>'+//名称
											'<div class="sizes"><h4>'+value.spec_key_name+'</h4></div>'+//规格属性
											'<div class="price"><span>￥ <span class="CartListTotalAmount">'+value.goods_price+'</span>'+//商品金额
												'<del>￥'+value.market_price+'</del></span>'+
												'<em>X <i class="goods_num">'+value.goods_num+'</i></em>'+//商品购买数量
											'</div>'+
										'</div>'+
									'</a>'+
								'</li>';
										
					
				});	//	end each
				
				$('#cartList').append(cartHtml);
				
				
				//优惠券生成
				$('#coupons').html('');
				var couponsHtml = '';
				if(response.data.coupons && response.data.coupons.length>0){//当优惠券有数据时
					
					$.each(response.data.coupons, function(key, value) {
							couponsHtml += '<li datacouponsid="'+value.id+'" datafull="'+value.condition+'" datamoney="'+value.money+'">'+
												'<h3>'+value.title+':满'+value.condition+'立减'+value.money+'元</h3>'+
												'<p>使用期限   '+value.date+'</p>'+
												'<i>&#xe61a;</i>'+
									       '</li>';
					});//	end each
					
					couponsHtml += '<li datacouponsid="0" datafull="0" datamoney="0">'+
										'<h3>不使用优惠券</h3>'+
										'<i>&#xe61a;</i>'+
									'</li>';					
					$('#coupons').append(couponsHtml);
					
					var couponsTitleHtml = '优惠券('+response.data.coupons.length+'张)';
					$('#couponsTitle').html(couponsTitleHtml);
					
				}else{
					$('#couponsTitle').html('优惠券(无可用优惠券)');
				}//优惠券结束
				
				
				
				//积分生成
				$('#user_credit').html('');
				var userCreditHtml = '';
				if(response.data.user_credit>0){
					userCreditHtml += '<strong>最高可用<i class="credit_user_credit">'+response.data.user_credit+'</i>积分</strong>'+
									   '<span>可抵扣<i class="credit_money_decimal">'+response.data.credit_money+'</i>元 <input class="creditCheck" name="" type="checkbox" value=""></span>'
				}else{
					userCreditHtml += '<strong>您目前没有积分</strong>';
				}
				$('#user_credit').append(userCreditHtml);
				
				//商品金额
				$('#total_money').html(response.data.money);
				$('#total_pay_money').html(response.data.money);	
				
				
			}
		});	
	}
	
	
	
	
	
	//优惠券选择确认
	$('.btn-full-red').click(function(){
		var datafull = parseFloat(parseFloat($('#coupon-light').find('.active').attr('datafull')).toFixed(2));
		var datamoney = parseFloat(parseFloat($('#coupon-light').find('.active').attr('datamoney')).toFixed(2));
		var total_money = parseFloat(parseFloat($('#total_money').html()).toFixed(2));
		
		
		if(total_money > datafull){
			$('#total_coupons').html(datamoney);
		}else{
			alert('商品金额不足以使用此优惠券!');
			$('#total_coupons').html('0');
		}		
		$('#coupon-light').hide();
		$('#coupon-black').hide();
		
		coundPay();
		
	});
	
	//优惠券选择	
	$('#coupon-light').delegate('#coupons li','click',function(){		
		$(this).addClass('active').siblings().removeClass('active');		
		$('#coupons li').each(function(){
			$(this).find('i').html('&#xe61a;');
		});
		$(this).find('i').html('&#xe619;');
	});
	
	//积分折扣
	$('#user_credit').delegate('.creditCheck','click',function(){
		var creditCheck = $(this).prop('checked');
		if(creditCheck){
			var total_credit = parseFloat($('.credit_money_decimal').html()).toFixed(2);
		}else{
			var total_credit = 0;
		}
		$('#total_credit').html(total_credit);
		
		coundPay();
		
	});
	
	
	//单击支付按钮
	$('.btn-full-green').click(function(){
		addOrder();
		//weixinPay(6);
	});
	
	
	$('.select-address').click(function(){
		var url = $(this).attr('data-url');
		
		//为选择地址回跳做URL
		var balanceUrl = 'html/shopping/balance.html';
		localStorage.setItem('balanceUrl',balanceUrl);	
		
		if(url == 1){
			location.href = Pub.getHtmlUrl("html/shopping/address-list.html");
		}else{
			location.href = Pub.getHtmlUrl("html/shopping/address.html");
		}
	});
	
	//计算总金额
	function coundPay(){
		var total_money = parseFloat($('#total_money').html()).toFixed(2);
		var total_coupons = parseFloat($('#total_coupons').html()).toFixed(2);
		var total_credit = parseFloat($('#total_credit').html()).toFixed(2);
		var total = parseFloat(total_money) - parseFloat(total_coupons) - parseFloat(total_credit);
		$('#total_pay_money').html(total);	
	}
	
	//生成地址HTML
	function addressHtml(adDetailed){
		var addressHtml = '';
		if(!adDetailed && typeof(adDetailed)!="undefined" && adDetailed!=0){//地址
			$('.select-address').attr('data-url',0);
			addressHtml += '<em>&#xe6e2;</em>'+
						   '<h4>添加收货地址</h4>'+
						   '<p class="warn">(您选择的地址可能造成订单的价格变化，请确认后再付款)</p>'+
						   '<i>&#xe6d5;</i>';				
		}else{
			$('.select-address').attr('data-url',1);
			addressHtml += '<em>&#xe6e2;</em>'+
						   '<h4>'+adDetailed.consignee+'&nbsp;&nbsp;'+adDetailed.mobile+'</h4>'+
						   '<p dataaddressid="'+adDetailed.id+'">'+adDetailed.province+' '+adDetailed.city+' '+adDetailed.district+' '+adDetailed.address+'</p>'+
						   '<i>&#xe6d5;</i>';
		}
		
		$('.select-address').html('');
		$('.select-address').append(addressHtml);//地址添加	
	}
	
	
	
	function addOrder(){
		/*_passport 	string 	必填 	用户票据
		address_id	int 	必填 	收货地址id
		cart_id 	array 	必填 	商品购物车id数组(从购物车结算必填此项) 如：（['1','4','5']）
		coupons_id	int 	选填 	优惠券id 0-不使用
		shipping_id	int 	选填 	配送方式id 0-免运费
		credit 	int 	选填 	使用积分数量
		postscript	string 	选填 	订单附言
		inv_payee 	string 	选填 	发票抬头 	*/
		var address_id = parseInt($('.select-address').find('p').attr('dataaddressid'));
		var cart_id = CartIdArray;
		var coupons_id = parseInt($('#coupon-light').find('.active').attr('datacouponsid'));
		
		if(isNaN(coupons_id)){
			coupons_id = 0;
		}
		
		var shipping_id = 0;
		
		//优惠券
		var creditCheck = $(this).prop('checked');
		if(creditCheck){
			var credit = parseInt($('.credit_user_credit').html()).toFixed(2);
		}else{
			var credit = 0;
		}
		
		
		
		//发票抬头		
		var inv_payee = $('#bill-show').find('.active').html();
		
		if( inv_payee == '单位发票'){
			inv_payee = $('.txt').val();
		}		
		var postscript = $('.textarea').val();
		

		//提交生成订单
		Pub.post('/Home/order/add ', 
			{
				cart_id:CartIdArray, 
				address_id:address_id,
				coupons_id:coupons_id,
				shipping_id:shipping_id,
				credit:credit,
				postscript:postscript,
				inv_payee:inv_payee/*,				
				_passport:password*/
			}, 
			function(response) {
				console.log(response);
				if (response.error_code == 0) {
					localStorage.removeItem('CartIdArray');//删除选中的购物车ID
					webToast('订单提交成功！',"middle",800);
					var order_id = response.data.order_id;
					
					weixinPay(order_id);//调用微信支付
							
				}else{
					webToast(response.message,"middle",1500);
				}
		});
		
	}
	
	
	//单独调用支付
	function weixinPay(order_id){
		order_id = parseInt(order_id);
		//提交订单支付
		Pub.post('/Home/Pay/toPay', {order_id:order_id/*,_passport:password*/},function(jsApi){
			console.log(jsApi);
			if (jsApi.error_code == 0) {
				webToast('正在跳转支付！',"middle",1000);
				jsApiParameters = jsApi.data.jsApiParameters;
				callpay();
				
			}else{//支付接口没调成功，应该跳到订单列表在次支付
				webToast(jsApi.message,"middle",1000);
			}
		});	//提交订单支付结束	
		
	}
	
	
	/*//微信付款
	function wxpay(){
            $.ajax({
                url:'http://gwshop.usrboot.com/home/pay/toPay',
                type:'POST',

                data:{'_passport':'cd830762319e54d2c70f4b4b5c47ffee','order_id':2},

                success:function(res){
                    console.log(res)
                    $("#parameters").val(res.data.jsApiParameters);


                    callpay();
                },
                error: function (da) {
                    alert('error');
                }
            })
      }*/

       
	
	
	
	
	

})


