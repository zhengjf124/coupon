/* 
 * 购物车
 *2016-6-28 
 *reliking
 * shopCartBoxId:'shopCartBox',//订单列表根目录ID
 * TotalAmountBoxId:'TotalAmount',//用于存放计算总金额的ID
 * 
 * 购物车列表必需要有的Class名称
 	bt_CartReduce 购物车减
 	Input_CartListNum 单个物品购买的数量
 	bt_CartPlus   购物车加
 	CartShopPrice 物品单价（用于结算的单价）
 	CartListTotalAmount  用于存放单个物品的总价
 	delCartList  删除物品
 * */



(function($){
	$.shopCart = function(cartConf){
		
		
		var objShopCartBoxId = $('#'+cartConf.shopCartBoxId);//订单列表根目录ID		
		var objTotalAmountBoxId = $('#'+cartConf.TotalAmountBoxId);//用于存放计算总金额的ID
	
	
	
		this.starShoppingCart = function(){	
			
			$('input[cartlevel=1]').bind("click",function(){
				var inputs = objShopCartBoxId.find('input[cartlevel=2]');		
				$(this).prop('checked') ? inputs.prop('checked',true) : inputs.prop('checked',false);		
				settotal();
			});
			
			objShopCartBoxId.delegate("input[cartlevel=2]","click",function(){
			//$('input[cartlevel=2]').delegate("click",function(){
				
				settotal();
			})
					
			
			//$('.bt_CartPlus').delegate("click",function(){
			objShopCartBoxId.delegate('.bt_CartPlus',"click",function(){
				var objNum = $(this).parent().find('.Input_CartListNum');
				var numVal = parseInt(objNum.val());
				
				var objShopPrice = $(this).parents('li').find('.CartShopPrice');
				var objCartTotalPrice = $(this).parents('li').find('.CartListTotalAmount');
						
				var ShopPrice = parseFloat(objShopPrice.html());		
				
				numVal++;
				objNum.val(numVal);	
				var cound = (ShopPrice*numVal).toFixed(2);
				
				objCartTotalPrice.text(cound);
				
				$(this).parents('li').find('.goods_num').html(numVal);
				
				settotal()					
			});
			
			
			//$('.bt_CartReduce').delegate("click",function(){		
			objShopCartBoxId.delegate('.bt_CartReduce',"click",function(){	
				var objNum = $(this).parent().find('.Input_CartListNum');
				var numVal = parseInt(objNum.val());
				
				var objShopPrice = $(this).parents('li').find('.CartShopPrice');
				var objCartTotalPrice = $(this).parents('li').find('.CartListTotalAmount');
						
				var ShopPrice = parseFloat(objShopPrice.html());		
				
				numVal--;
				if(numVal<1){
					numVal = 1;	
				}
				objNum.val(numVal);	
				var cound = (ShopPrice*numVal).toFixed(2);
				
				objCartTotalPrice.text(cound);
				
				
				$(this).parents('li').find('.goods_num').html(numVal);
				
				settotal()					
			});
			
			//$('.delCartList').delegate("click",function(){
			objShopCartBoxId.delegate('.delCartList',"click",function(){						
				var conf = confirm('确定删除此商品吗？');
				
				if (conf) {
					var inputs = $(this).parents('li').find('input[cartlevel=2]');
					var cartListId = inputs.val();
					var ObjLi = $(this).parents('li');	
					Pub.post('/Home/Cart/delete', {/*_passport:password,*/id:cartListId}, function(response) {
						if (response.error_code == 0) {				
							ObjLi.remove();								
							webToast("删除成功","middle",2000);
						}
					});
					settotal();
				}	
			})
			
								
		}
		
		//计算总数
		function settotal(){
			var total = 0;
			var selectNum = 0;
			var chooleCartId = new Array();
			var cartI = 0;
			$('#'+cartConf.shopCartBoxId + ' li').each(function(){
				var td_checkbox = $(this).find('input[cartlevel=2]').prop('checked');	
				if(td_checkbox){					
					var ShopPrice = parseFloat($(this).find('.CartShopPrice').html());
					var numVal = parseInt($(this).find('.Input_CartListNum').val());
					var t = parseFloat((ShopPrice * numVal).toFixed(2));
					total = (parseFloat(total) + t).toFixed(2);
					selectNum ++;
					
					chooleCartId[cartI] = parseInt($(this).find('input[cartlevel=2]').val());
					cartI ++;
				}
			});
			$('#selectNum').html(selectNum);			
			objTotalAmountBoxId.html('￥' + total);
			var CartIdArray = JSON.stringify(chooleCartId);//把数组或JSON对象转为字符串
			localStorage.setItem("CartIdArray",CartIdArray);//	
				
		}	
	}
	
})(jQuery);
