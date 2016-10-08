;
(function($, window, document, undefined) {
    var Page = function() {
        this.init();
    };
    Page.prototype = {
        init: function() {
            this.getData();
        },
        getData: function() {
            var _this = this;
            Pub.get('/Home/index/index', {}, function(response) {
                if (response.error_code === 0) {
                    _this.advData(response.data.adv,_this.advDataEvent);
                    _this.cateData(response.data.cate);
                    _this.buyGoodsData(response.data.buy_goods.list,response.data.buy_goods.limit_time,_this.buyGoodsDataEvent);
                    _this.remommendData(response.data.recommend);
                    _this.hotGoodsData(response.data.hot_goods);
                }
            });
        },
        advData: function(data,callback) {
            $("#adv").empty();
            var html = '';
            if (data && data.length > 0) {
                html = '<div class="focus">' +
                    '<a class="arrow-left"><i>&#xe603;</i></a>' +
                    '<a class="arrow-right"><i>&#xe604;</i></a>' +
                    '<div class="swiper-main">' +
                    '<div class="swiper-container swiper1">' +
                    '<ul class="swiper-wrapper">';
                for (var i in data) {
                    html += '<li class="swiper-slide"><a href="' + data[i].ad_link + '"><img src="' + data[i].ad_code + '" alt="' + data[i].ad_name + '"></a></li>';
                }
                html += '</ul>' +
                    '</div>' +
                    '</div>' +
                    '<div class="pagination pagination1"></div>' +
                    '</div>';
                $("#adv").append(html);
                callback();
            }
        },
        advDataEvent: function() {
            var swiper = new Swiper('.swiper1', {
                pagination: '.pagination1',
                loop: true,
                grabCursor: true
            });
            $('.arrow-left').click(function(e) {
                e.preventDefault()
                swiper.swipePrev()
            });
            $('.arrow-right').click(function(e) {
                e.preventDefault()
                swiper.swipeNext()
            });
            $('.pagination1 .swiper-pagination-switch').click(function() {
                swiper.swipeTo($(this).index())
            })
        },
        cateData: function(data){   
            $("#cate").empty;
            var html = '';
            if(data && data.length > 0){
                html = '<div class="icons">' +
                '<ul>';
                for (var i in data) {
                    html += '<li><a href="' + Pub.getHtmlUrl("html/goods/category.html") + '?id=' + data[i].id + '"><img src="' + data[i].type_img + '"><p>' + data[i].type_name + '</p></a></li>';
                }
                html += '</ul>' +
                    '</div>';
                $("#cate").append(html);    
            }
        },
        buyGoodsData: function(data,time,callback){
            $("#buyGoods").empty();
            var html = '';
            if(data && data.length > 0){
                html += '<div class="flash-sale">' +
                '<div class="sale-top">' +
                '<strong>限时抢购</strong>' +
                '<div class="time" id="buyTime">' +
                '<a>距离结束</a>';
                html += '<span class="day">0</span>天<span class="hour">00</span>:<span class="minute">00</span>:<span class="second">00</span>' +
                    '</div>' +
                    '</div>';
                html += '<div class="sale-list">' +
                    '<ul>';
                for (var i in data) {
                    html += '<li>' +
                        '<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id=' + data[i].id + '">' +
                        '<div class="img">' +
                        '<span></span><img src="' + data[i].goods_img + '">' +
                        '</div>' +
                        '<div class="info">' +
                        '<div class="name">' +
                        '<h3>' + data[i].goods_name + '</h3></div>' +
                        '<div class="price">' +
                        '<h4>￥<strong>' + data[i].goods_price + '</strong></h4>' +
                        '<p><del>￥' + data[i].market_price + '</del></p>' +
                        '</div>' +
                        '</div>' +
                        '</a>' +
                        '</li>';
                }
                html += '</ul>' +
                    '</div>' +
                    '</div>';
                $("#buyGoods").append(html);
                callback(time);

            }
        },
        buyGoodsDataEvent: function(time){
            Pub.countDown(time, "#buyTime")
        },
        remommendData: function(data){
            $("#remommend").empty();
            var html = '';
            if(data && data.length > 0){
                html += '<div class="list-lg">' +
                '<ul>';
                for (var i in data) {
                    html += '<li>' +
                        '<div class="img">' +
                        '<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id=' + data[i].id + '"><img src="' + data[i].goods_img + '"></a>' +
                        '</div>' +
                        '<div class="info">' +
                        '<div class="name">' +
                        '<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id=' + data[i].id + '">' +
                        '<h3>' + data[i].goods_name + '</h3>' +
                        '<h4>' + data[i].goods_brief + '</h4>' +
                        '</a>' +
                        '</div>' +
                        '<div class="price">' +
                        '<strong>￥&nbsp;' + data[i].goods_price + '</strong>' +
                        '<span>' +
                        '<a><i>&#xe6b6;</i>' + data[i].click_count + '</a>' +
                        '<a><i>&#xe644;</i>' + data[i].comment_count + '</a>' +
                        '<a><i>&#xe67e;</i>' + data[i].favorite_count + '</a>' +
                        '</span>' +
                        '</div>' +
                        '</div>' +
                        '</li>';
                }
                html += '</ul>' +
                    '</div>';
                $("#remommend").append(html);

            }
        },
        hotGoodsData: function(data){
            $("#hotGoods").empty();
            var html = '';
            if(data && data.length > 0){
                html = '<div class="hot-sale">' +
                '<div class="hot-sale-title">' +
                '<i>&#xe6e3;</i><strong>热卖单品</strong>' +
                '</div>';
                html += '<div class="list-sm">' +
                    '<ul>';
                for (var i in data) {
                    html += '<li>' +
                        '<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id=' + data[i].id + '">' +
                        '<div class="img">' +
                        '<i>热卖</i><span></span><img src="' + data[i].goods_img + '">' +
                        '</div>' +
                        '<div class="info">' +
                        '<div class="name">' +
                        '<h4>' + data[i].goods_name + '</h4>' +
                        '</div>' +
                        '<div class="price">' +
                        '<h4>￥' + data[i].goods_price + '</h4>' +
                        '</div>' +
                        '</div>' +
                        '</a>' +
                        '</li>';
                }
                html += '</ul>' +
                    '</div>' +
                    '</div>';
                $("#hotGoods").append(html);

            }
        }
    };
    var page = new Page();
})(jQuery, window, document)
