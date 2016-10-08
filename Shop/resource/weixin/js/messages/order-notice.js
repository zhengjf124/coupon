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
            Pub.post('/Home/NoticeOrder/index', {}, function(response) {
                if (response.error_code === 0) {
                    _this.noticeData(response.data.list);
                }
            });
        },
        noticeData: function(data) {
            $('#order_notice').empty();
            var html = '';
            if (data && data.length > 0) {
                for (var i in data) {
                    html += '<li>' +
                        '<div class="time">' + data[i].add_time + '</div>' +
                        '<div class="box">' +
                        '<a href="###">' +
                        '<h4>' + data[i].title + '</h4>' +
                        '<div class="content">' + data[i].content + '</div>' +
                        '</a>' +
                        '</div>' +
                        '</li>';
                }
                $('#order_notice').append(html);
            }else{
            	$('#order_notice').append('<div class="nothing">暂无消息！</div>');
            }
        }
    };
    var page = new Page();
})(jQuery, window, document)
