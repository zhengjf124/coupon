//(function($){
//	function Page(){
//		this.init();
//	}
//	Page.prototype = function(){
//		init: function(){
//			var search = Pub.searchToObject();
//		},
//		getDetailData: function(){
//			var _this = this;
//			if(_this.search.id){
//				
//			}
//		}
//	}
//
//	new Page();
//})(jQuery)

var conts = null,
	imgs = null,
	comments = null,
	likeData = null,
	contentData = null,
	cartData = null,
	buyData = null,
	collectData = null,
	goods_spec = {},
	prices_spec = null,
	commentData = null,
	goods_store = 0;
var html = '',
	htmlLike = '',
	htmlCont = '',
	htmlBuy = '',
	htmlCart = '';
var search = Pub.searchToObject();
var commentParams = {
	goods_id: search.id
};
var active = true;

$(document).ready(function() {
	$("#shangpin-show").empty();
	$("#xiangqing-show").empty();
	$("#pingjia-show").empty();
	$("#buy-light").empty();
	if(search.id) {
		Pub.post('/Home/Goods/detail', {
				id: search.id
			}, function(response) {
				if(response.error_code == 0) {
					
					conts = response.data.goods;
					imgs = response.data.goods_images;
					comments = response.data.goods_comment;
					prices_spec = response.data.spec_goods_price;
					goods_store = response.data.goods.store_count;
					goodsPics();
					goodsInfo();
					goodsOther();

					$("#shangpin-show").append(html);
					//Main Swiper
					var swiper = new Swiper('.swiper1', {
						pagination: '.pagination1',
						loop: true,
						grabCursor: true
					});
					//Navigation arrows
					$('.arrow-left').click(function(e) {
						e.preventDefault()
						swiper.swipePrev()
					});
					$('.arrow-right').click(function(e) {
						e.preventDefault()
						swiper.swipeNext()
					});
					//Clickable pagination
					$('.pagination1 .swiper-pagination-switch').click(function() {
						swiper.swipeTo($(this).index())
					})

				}
			})
			//商品
		$("#shangpin").on("click", function() {
			$(this).siblings().removeClass("active");
			$(this).addClass("active");
			$("#shangpin-show").empty();
			$("#likes").empty();
			$("#xiangqing-show").empty();
			$("#pingjia-show").empty();
			Pub.post('/Home/Goods/detail', {
				id: search.id
			}, function(response) {
				if(response.error_code == 0) {
					$("#shangpin-show").empty();
					conts = response.data.goods;
					imgs = response.data.goods_images;
					comments = response.data.goods_comment;
					goodsPics();
					goodsInfo();
					goodsOther();
					$("#shangpin-show").append(html);
					//Main Swiper
					var swiper = new Swiper('.swiper1', {
						pagination: '.pagination1',
						loop: true,
						grabCursor: true
					});
					//Navigation arrows
					$('.arrow-left').click(function(e) {
						e.preventDefault()
						swiper.swipePrev()
					});
					$('.arrow-right').click(function(e) {
						e.preventDefault()
						swiper.swipeNext()
					});
					//Clickable pagination
					$('.pagination1 .swiper-pagination-switch').click(function() {
						swiper.swipeTo($(this).index())
					})

				}
			})

			//猜你喜欢
			$("#likes").empty();
			Pub.post('/Home/Goods/youLike', {}, function(response) {
				if(response.error_code == 0) {
					likeData = response.data.goods_list;
					goodsLike();
					$("#likes").append(htmlLike);
				}
			})

		})

		//详情
		$("#xiangqing").on("click", function() {
				$(this).siblings().removeClass("active");
				$(this).addClass("active");
				$("#shangpin-show").empty();
				$("#likes").empty();
				$("#pingjia-show").empty();
				Pub.post('/Home/Goods/content', {
					id: search.id
				}, function(response) {
					if(response.error_code == 0) {
						contentData = response.data;
						goodsContent();
						$("#xiangqing-show").empty();
						$("#xiangqing-show").append(htmlCont);
					}
				})

			})
			//评价
		$("#pingjia").on("click", function() {
			$(this).siblings().removeClass("active");
			$(this).addClass("active");
			$("#shangpin-show").empty();
			$("#likes").empty();
			$("#xiangqing-show").empty();
			$("#pingjia-show").empty();
			getComment();
		})

	}
	//猜你喜欢
	$("#likes").empty();
	Pub.post('/Home/Goods/youLike', {}, function(response) {
		if(response.error_code == 0) {
			likeData = response.data.goods_list;
			goodsLike();
			$("#likes").append(htmlLike);
		}
	})

	//加入购物车
	$("#addToCart").on("click", function() {
		Pub.post('/Home/Goods/detail', {
			id: search.id
		}, function(response) {
			if(response.error_code == 0) {
				cartData = response.data;
				goodsCart();
				var num = $("#txt-num").val();
				var total = goods_store;
				if(num == 1) {
					$("#btn-minus").addClass("disabled");
				}
				if(num == goods_store) {
					$("#btn-plus").addClass("disabled");
				}
				$("#btn-plus").on("click", function() {
					if(num < goods_store) {
						$("#btn-minus").removeClass("disabled");
						num++;
						total--;
						$("#store").text(total-1);
						$("#txt-num").val(num);
					} else if(num == goods_store) {
						$("#btn-plus").addClass("disabled");
						webToast("对不起，库存不够！", "middle", 2000);
					}
				})
				$("#btn-minus").on("click", function() {
					if(num > 1) {
						$("#btn-plus").removeClass("disabled");
						num--;
						total++;
						$("#store").text(total-1);
						$("#txt-num").val(num);
					} else if(num == 1) {
						$("#btn-minus").addClass("disabled");
						webToast("对不起，数量不能小于1！", "middle", 2000);
					}
				})
				$("#cart-black").fadeIn();
				$("#cart-light").slideDown();
				$("html").css("overflow-y", "hidden");
			}
		})

	});
	$("#cart-black").on("click", function() {
		$("#cart-light").slideUp();
		$("#cart-black").fadeOut();
		$("html").css("overflow-y", "auto");
	});
	//立即购买
	$("#buyNow").on("click", function() {
		$('#cart-light').empty();
		Pub.post('/Home/Goods/detail', {
			id: search.id
		}, function(response) {
			if(response.error_code == 0) {
				buyData = response.data;
				goodsBuy();
				var num = $("#txt-num").val();
				if(num == 1) {
					$("#btn-minus").addClass("disabled");
				}
				if(num == 12) {
					$("#btn-plus").addClass("disabled");
				}
				$("#btn-plus").on("click", function() {
					if(num < 12) {
						$("#btn-minus").removeClass("disabled");
						num++;
						$("#txt-num").val(num);
					} else if(num == 12) {
						$("#btn-plus").addClass("disabled");
						webToast("对不起，库存不够！", "middle", 2000);
					}
				})
				$("#btn-minus").on("click", function() {
					if(num > 1) {
						$("#btn-plus").removeClass("disabled");
						num--;
						$("#txt-num").val(num);
					} else if(num == 1) {
						$("#btn-minus").addClass("disabled");
						webToast("对不起，数量不能小于1！", "middle", 2000);
					}
				})
				$("#buy-black").fadeIn();
				$("#buy-light").slideDown();
				$("html").css("overflow-y", "hidden");
			}
		})

	});
	$("#buy-black").on("click", function() {
		$("#buy-light").slideUp();
		$("#buy-black").fadeOut();
		$("html").css("overflow-y", "auto");
	});
	//收藏
	collectCheck();
	$("#collect").on("click", function() {
		if($("#collect").is(".active")) {
			collectRemove();
		} else {
			collectAdd();
		}
	});

})

function goodsPics() {
	if(imgs) {
		html = '<div class="focus">' +
			'<a class="arrow-left"><i>&#xe603;</i></a >' +
			'<a class="arrow-right"><i>&#xe604;</i></a>' +
			'<div class="swiper-main">' +
			'<div class="swiper-container swiper1">' +
			'<ul class="swiper-wrapper">';
		for(var i in imgs) {
			html += '<li class="swiper-slide"><img src="' + imgs[i].image_url + '"></li>';
		}
		html += '</ul>' +
			'</div>' +
			'</div>' +
			'<div class="pagination pagination1"></div>' +
			'</div>';
	}
}

function goodsInfo() {
	if(conts) {
		html += '<div class="goods-info">' +
			'<div class="name">' +
			'<h3>' + conts.goods_name + '</h3>' +
			'</div>' +
			'<div class="times">' +
			'<span>' + conts.goods_brief + '</span>' +
			'</div>' +
			'<div class="price-storage">' +
			'<div class="price"><span>￥<strong>' + conts.goods_price + '</strong></span><del>￥' + conts.market_price + '</del></div>' +
			'<div class="storage">' +
			'<h5>评价 ' + comments + '<span>|</span>库存 <em id="store">' + (conts.store_count - 1) + '</em></h5>' +
			'</div>' +
			'</div>' +
			'</div>';
	}
}

function goodsOther() {
	if(conts) {
		html += '<div class="goods-info-other">' +
			'<p><strong>促销</strong>';
		if(conts.is_new && conts.is_new == 1) {
			html += '<span><font>新</font>最新推出</span>';
		}
		if(conts.is_hot && conts.is_hot == 1) {
			html += '<span><font class="hot">热</font>热门商品</span>';
		}
		html += '</p>' +
			'</div>';
	}
}

function goodsLike() {
	if(likeData) {
		htmlLike = '<div class="maybe-like">' +
			'<h3>你可能会喜欢</h3>' +
			'</div>';
		htmlLike += '<div class="list-sm">' +
			'<ul>';
		for(var i in likeData) {
			htmlLike += '<li>' +
				'<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id=' + likeData[i].id + '">' +
				'<div class="img">' +
				'<span></span><img src="' + likeData[i].goods_img + '">' +
				'</div>' +
				'<div class="info">' +
				'<div class="name">' +
				'<h4>' + likeData[i].goods_name + '</h4>' +
				'</div>' +
				'<div class="price">' +
				'<h4>￥' + likeData[i].goods_price + '</h4>' +
				'</div>' +
				'</div>' +
				'</a>' +
				'</li>';
		}
		htmlLike += '</ul>' +
			'</div>';

	}

}

function goodsContent() {
	if(contentData) {
		var c = contentData.goods_desc.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"');
		console.log(contentData.goods_desc);
		htmlCont = '<div class="goods-detail">' + c + '</div>';
	}
}

//加入购物车
function goodsCart() {
	if(cartData) {
		$("#cart-light").empty();
		htmlCart = '<div class="goods-buy-info">' +
			'<div class="img">' +
			'<span></span>';
		if(cartData.goods_images.length > 0) {
			html += '<img src="' + cartData.goods_images[0].image_url + '">';
		} else {
			html += '';
		}
		htmlCart += '</div>' +
			'<div class="info">' +
			'<div class="name">' +
			'<a>' +
			'<h3>' + cartData.goods.goods_name + '</h3></a>' +
			'</div>' +
			'<div class="price">' +
			'<span>￥<strong><em id="price_spec1">' + cartData.goods.goods_price + '</em></strong></span><del>￥' + cartData.goods.market_price + '</del>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'<div class="spec">' +
			'<div class="spec-container">';
		for(var i in cartData.specList) {
			goods_spec[i] = null;
			htmlCart += '<dl>' +
				'<dt>' + i + '</dt>' +
				'<dd>';
			for(var j in cartData.specList[i]) {
				htmlCart += '<label onclick="selectGoodsSpec(\'' + i + '\', ' + cartData.specList[i][j].item_id + ')">' + cartData.specList[i][j].item + '</label>';
			}
			htmlCart += '</dd>' +
				'</dl>';
		}
		htmlCart += '</div>' +
			'</div>' +
			'<div class="qty">' +
			'<div class="qty-container">' +
			'<span>购买数量</span>' +
			'<div class="qty-form">' +
			'<button id="btn-minus"><i>&minus;</i></button>' +
			'<input type="text" value="1" id="txt-num" class="disabled"/>' +
			'<button id="btn-plus"><i>&#43;</i></button>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'<button class="btn-full-red" id="submit-cart">确定</button>';
		$("#cart-light").append(htmlCart);

		var aDl = $(".spec-container dl");
		var a = 1;
		for(a = 1; a <= aDl.length; a++) {
			$(".spec-container dl:nth-child(" + a + ") dd label").on("click", function() {
				$(this).siblings().removeClass("active");
				$(this).addClass("active");
			})
		}

		$("#submit-cart").on("click", function() {
			var gNum = $("#txt-num").val();
			var cartParams = {
				goods_id: search.id,
				goods_num: gNum,
				goods_spec: goods_spec,
				buy_type: "cart"
			}
			for(var i in goods_spec) {
				if(goods_spec[i] == null) {
					webToast("请选择" + i + "参数", "middle", 2000);
					return false;
				}
			}
			Pub.post('/Home/Cart/add', cartParams, function(response) {
				if(response.error_code == 0) {
					webToast("已加入购物车", "middle", 2000);
					$("#cart-light").slideUp();
					$("#cart-black").fadeOut();
					$("html").css("overflow-y", "auto");
				}
			})
		});

	}
}

//购买
function goodsBuy() {
	if(buyData) {
		$("#buy-light").empty();
		htmlBuy = '<div class="goods-buy-info">' +
			'<div class="img">' +
			'<span></span><img src="' + buyData.goods_images[0].image_url + '">' +
			'</div>' +
			'<div class="info">' +
			'<div class="name">' +
			'<a>' +
			'<h3>' + buyData.goods.goods_name + '</h3></a>' +
			'</div>' +
			'<div class="price">' +
			'<span>￥<strong><em id="price_spec2">' + buyData.goods.goods_price + '</em></strong></span><del>￥' + buyData.goods.market_price + '</del>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'<div class="spec">' +
			'<div class="spec-container">';
		for(var i in buyData.specList) {
			goods_spec[i] = null;
			htmlBuy += '<dl>' +
				'<dt>' + i + '</dt>' +
				'<dd>';
			for(var j in buyData.specList[i]) {
				htmlBuy += '<label onclick="selectGoodsSpec(\'' + i + '\', ' + buyData.specList[i][j].item_id + ')">' + buyData.specList[i][j].item + '</label>';
			}
			htmlBuy += '</dd>' +
				'</dl>';
		}
		htmlBuy += '</div>' +
			'</div>' +
			'<div class="qty">' +
			'<div class="qty-container">' +
			'<span>购买数量</span>' +
			'<div class="qty-form">' +
			'<button id="btn-minus"><i>&minus;</i></button>' +
			'<input type="text" value="1" id="txt-num" class="disabled"/>' +
			'<button id="btn-plus"><i>&#43;</i></button>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'<button class="btn-full-red" id="submit-buy">确定</button>';
		$("#buy-light").append(htmlBuy);

		var aDl = $(".spec-container dl");
		var a = 1;
		for(a = 1; a <= aDl.length; a++) {
			$(".spec-container dl:nth-child(" + a + ") dd label").on("click", function() {
				$(this).siblings().removeClass("active");
				$(this).addClass("active");
			})
		}

		$("#submit-buy").on("click", function() {
			var gNum = $("#txt-num").val();
			var buyParams = {
					goods_id: search.id,
					goods_num: gNum,
					goods_spec: goods_spec,
					buy_type: "buying"
				}
				// 判断规格没选
			for(var i in goods_spec) {
				if(goods_spec[i] == null) {
					webToast("请选择" + i + "参数", "middle", 2000);
					return false;
				}
				Pub.post('/Home/Cart/add', buyParams, function(response) {
					if(response.error_code == 0) {
						location.href = Pub.getHtmlUrl('html/shopping/balance.html') + '?id=' + response.data.cart_id;
					}
				})
			}
		});

	}
}
//规格选择、价格变化
function selectGoodsSpec(index, value) {
	goods_spec[index] = value;
	var spec = [];
	for(var i in goods_spec) {
		spec.push(goods_spec[i]);
	}
	spec = spec.join('_');
	var spec_price = prices_spec[spec].price;
	$("#price_spec1").text(spec_price);
	$("#price_spec2").text(spec_price);
}

//收藏
function collectCheck() {
	Pub.post('/Home/Collect/isCollect', {
		goods_id: search.id
	}, function(response) {
		if(response.error_code == 0) {
			if(response.data.status == 1) {
				$("#collect").addClass("active");
			} else if(response.data.status == 0) {
				$("#collect").removeClass("active");
			}
		}
	})
}

function collectAdd() {
	Pub.post('/Home/Collect/add', {
		goods_id: search.id
	}, function(response) {
		$("#collect").addClass("active");
		if(response.error_code == 0) {
			webToast("收藏成功", "middle", 2000);
		}
	});
}

function collectRemove() {
	Pub.post('/Home/Collect/cancel', {
		goods_id: search.id
	}, function(response) {
		$("#collect").removeClass("active");
		if(response.error_code == 0) {
			webToast("已取消收藏", "middle", 2000);
		}
	});
}
//评论列表
function getComment() {
	if(active) {
		Pub.post('/Home/Comments/goods_comments', commentParams, function(response) {
			if(response.error_code == 0) {
				
				//				commentParams.offset += commentParams.limit;
				commentList(response.data);
			}
		})
	}
}

function commentList(data) {
	var html = '';
	if(data.list.length > 0) {
		html = '<div class="judge-bottom">';
		html += '<div class="judge-score">' +
			'<h3>总体评分</h3>' +
			'<p id="stars">';
		for(var x = 0; x < 5; x++) {
			if(x < data.total_score) {
				html += '<i class="active">&#xe62d;</i>';
			} else {
				html += '<i>&#xe62d;</i>';
			}
		}
		html += '</p>' +
			'</div><br />';

		html += '<div class="judge-list">' +
			'<h3>商品评价</h3>' +
			'<ul>';
		for(var i in data.list) {
			html += '<li>' +
				'<div class="name">' +
				'<img src="' + data.list[i].headimgurl + '"><strong>' + data.list[i].user_name + '</strong><span>' + data.list[i].add_time + '</span>' +
				'</div>' +
				'<div class="brief">' +
				'<h5>' + data.list[i].content + '</h5>' +
				'</div>' +
				'<div class="imgs">';
                for(var j=0; j<data.list[i].comment_img.length; j ++){
                	html += '<img src="'+data.list[i][j]+'">';
                }
			html +=	'</div>'+
				'</li>';
		}
		html += '</ul>' +
			'</div>' +
			'</div>';
		$("#pingjia-show").append(html);

	} else {
		active = false;
		$("#pingjia-show").append('<div class="nothing">抱歉，暂无评价！</div>')
	}

}