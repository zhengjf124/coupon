(function($) {
    function Page() {
        this.init();
    }
    Page.prototype = {
        init: function() {
            var self = this;
            $("#category").empty();
            this.getData().then(function() {
                self.initEvent();
            });
        },
        getData: function() {
            var self = this;
            return Pub.get('/Home/goodsCategory/index').then(function(response) {
                var categoryData, secondCategoryData;
                if (response.error_code === 0) {
                    console.log(response);
                    categoryData = response.data.list;
                    if (categoryData && categoryData.length > 0) {
                        secondCategoryData = categoryData[0].next;
                        hotData = categoryData[0].hot_goods;
                    }
                }
                $("#kind").empty();
                self.categoryData = categoryData;
                self.categoryOne(categoryData);
                self.categoryTwo(secondCategoryData);
                self.hotGoods(hotData);
            });
        },
        categoryOne: function(categoryData) {
            if (categoryData) {
                var html = '';
                for (var i in categoryData) {
                    html += '<li>' +
                        '   <a href="javascript:void(0)" data-index="' + i + '">' +
                        '       <i><img src="' + categoryData[i].type_img + '"></i>' +
                        '       <h5>' + categoryData[i].type_name + '</h5>' +
                        '   </a>' +
                        '</li>';
                }
                $("#category").append(html);
            }
        },
        categoryTwo: function(secondCategoryData) {
            if (secondCategoryData) {
                var html = '';
                for (var i in secondCategoryData) {
                    html += '<h4>' + secondCategoryData[i].type_name + '</h4>';
                    if (secondCategoryData[i].next && secondCategoryData[i].next.length > 0) {
                        html += '<ul class="hot-kind">';
                        var threeCate = secondCategoryData[i].next;
                        for (var j in threeCate) {
                            html += '<li>' +
                                '   <a href="' + Pub.getHtmlUrl("html/goods/list.html") + '?id=' + threeCate[j].id + '">' +
                                '       <div class="img">' +
                                '           <span></span><img src="' + threeCate[j].type_img + '">' +
                                '       </div>' +
                                '       <div class="name">' + threeCate[j].type_name + '</div>' +
                                '   </a>' +
                                '</li>';
                        }
                        html += '</ul>';
                    }
                }
                $("#kind").append(html);
            }
        },
        hotGoods: function(data) {
            $("#hot_goods").empty();
            var html = '';
            if (data && data.length > 0) {
                html += '<h4>热门单品</h4>' +
                    '<ul class="hot-list">';
                for (var i in data) {
                    html += '<li>' +
                        '<a href="'+Pub.getHtmlUrl("html/goods/cont.html")+'?id='+data[i].id+'">' +
                        '<div class="img">' +
                        '<span></span><img src="'+data[i].goods_img+'">' +
                        ' </div>' +
                        '<div class="info">' +
                        '<div class="name">'+data[i].goods_name+'</div>' +
                        '<div class="price">' +
                        '<span>￥'+data[i].goods_price+'</span><del>￥'+data[i].market_price+'</del>' +
                        '</div>' +
                        '</div>' +
                        '</a>' +
                        '</li>';
                }
                html += '</ul>';
                $("#hot_goods").append(html);
            }

        },
        //相关事件绑定
        initEvent: function() {
            var self = this;

            //点击效果
            $("#category li").on("click", function() {
                $(this).siblings().removeClass("active");
                $(this).addClass("active");
            });

            //滚动条样式
            $(".category-menu").niceScroll({
                cursorcolor: "",
                cursoropacitymax: 1,
                touchbehavior: true,
                cursorwidth: "5px",
                cursorborder: "0",
                cursorborderradius: "5px"
            });

            $("#category li > a").on("click", function() {
                var index = $(this).attr("data-index");
                $("#kind").empty();
                if (self.categoryData && self.categoryData[index]) {
                    var secondCategoryData = self.categoryData[index].next;
                    var hotData = self.categoryData[index].hot_goods;
                    console.log(hotData);
                    self.categoryTwo(secondCategoryData);
                    self.hotGoods(hotData);
                }
            });
        }
    };

    $(document).ready(function() {
        new Page();
    });
})(jQuery);
