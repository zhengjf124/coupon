/*
 * 用户中心逻辑
 */
(function($) {

    function Page() {
        this.init();
    }
    Page.prototype = {
        data: '',
        init: function() {
            this.getData();
            this.initEvent();
        },
        getData: function() {
            var self = this;
            Pub.post("/Home/user/detail").then(function(data) {
                self.data = data.data;
                self.initEvent();
            });
        },
        //相关事件绑定及操作
        initEvent: function() {
            $("#nick_name").html(this.data.nick_name);
            $("#avatar").attr('src',this.data.headimgurl);
        }
    };

    $(document).ready(function() {
        var page = new Page();
    });

})(jQuery);
