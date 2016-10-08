;(function($,undefined) {
    function Page() {
        this.init();
    }
    Page.prototype = {
        init: function() {
            this.getData();
        },
        getData: function() {
            var _this = this;
            Pub.post('/Home/Collect/index', {}, function(response) {
                if (response.error_code == 0) {
                    _this.favoritesList(response.data.list);
                    _this.initEvent();
                }
            });
        },
        favoritesList: function(data) {
            $("#favorites_list").empty();
            var html = '';
            if (data && data.length > 0) {
                for (var i in data) {
                    html += '<li>' +
                        '<div class="favorites-cancel" data-index="' + data[i].goods_id + '">' +
                        '<i>&#xe6c5;</i>' +
                        '<p>取消收藏</p>' +
                        '</div>' +
                        '<a href="' + Pub.getHtmlUrl("html/goods/cont.html") + '?id=' + data[i].goods_id + '">' +
                        '<div class="img">' +
                        '<span></span><img src="' + data[i].goods_img + '">' +
                        '</div>' +
                        '<div class="info">' +
                        '<div class="name">' + data[i].goods_name + '</div>' +
                        '<div class="brief">' +
                        '<h5>' + data[i].goods_brief + '</h5>' +
                        '</div>' +
                        '<div class="tag">';
                    if (data[i].is_hot == 1) {
                        html += '<span class="hot">热卖</span>';
                    };
                    if (data[i].is_new == 1) {
                        html += '<span class="new">新品</span>';
                    }
                    html += '</div>' +
                        '<div class="price">' +
                        '<span>￥' + data[i].goods_price + '</span><del>￥' + data[i].market_price + '</del><em>评论' + data[i].comment_count + '条</em>' +
                        '</div>' +
                        '</div>' +
                        '</a>' +
                        '</li>';
                }
                $("#favorites_list").append(html);
            } else {
                $("#favorites_list").append('<div class="nothing">暂无收藏！</div>');
            }
        },
        initEvent: function() {
            $(".favorites-cancel").on("click", function() {
                var index = $(this).attr("data-index");
                var _this = this;
                cloud.asked('您确定要取消收藏吗？', function() {
                    Pub.post('/Home/Collect/cancel', { goods_id: index }, function(response) {
                        if (response.error_code == 0) {
                            cloud.explain('您已取消收藏！', 'success', 1500);
                            $(_this).parent().fadeOut();
                        }
                    });

                }, function() {
                    cloud.msg('您选择了取消！', '50%', 1500);
                });



            })
        }

    }
    new Page();
})(jQuery)
