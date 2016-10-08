;
(function($, window, document, undefined) {
    var Page = function() {
        this.init();
    };
    Page.prototype = {
        params: {
            id: 0,
            consignee: '',
            mobile: '',
            province: '',
            city: '',
            district: '',
            address: ''
        },
        init: function() {
            this.initParams();
            this.getData();
            this.initEvent();
        },
        initParams: function() {
            var search = Pub.searchToObject();
            this.params.id = search.id;     
        },
        getData: function() {
            var _this = this;
            Pub.post('/Home/UserAddress/detail', { id: _this.params.id }, function(response) {
                if (response.error_code === 0) {
                    _this.addressData(response.data);
                }
            });
        },
        addressData: function(data) {
            if (data) {
                $("#consignee").val(data.consignee);
                $("#mobile").val(data.mobile);
                $("#province").val(data.province);
                $("#city").val(data.city);
                $("#district").val(data.district);
                $("#address").val(data.address);
            } else {
                cloud.msg('抱歉，参数错误！', '50%', 2000);
            }
        },
        initEvent: function() {
            var _this = this;
            $.Tipmsg.r = null;
            $('#address-form').Validform({
                tiptype: function(msg) {
                    layer.msg(msg);
                }
            });
            $('#submit_edit').on('click', function() {
                if (consignee == "" || mobile == "" || province == "" || city == "" || district == "" || address == "") {
                    layer.msg(msg);
                } else {
                    _this.postData();
                }
                return false;
            });

        },
        postData: function() {
            var _this = this;
            _this.params.consignee = $("#consignee").val();
            _this.params.mobile = $("#mobile").val();
            _this.params.province = $("#province").val();
            _this.params.city = $("#city").val();
            _this.params.district = $("#district").val();
            _this.params.address = $("#address").val();
            Pub.post('/Home/UserAddress/update',{
            	id: _this.params.id,
            	consignee: _this.params.consignee,
            	province: _this.params.province,
            	city: _this.params.city,
            	district: _this.params.district,
            	address: _this.params.address,
            	mobile: _this.params.mobile
            }, function(response) {
                if (response.error_code === 0) {
                    cloud.msg('恭喜您，修改成功！', '50%', 2000);
                    setTimeout(function() {
                        location.href = Pub.getHtmlUrl('html/shopping/address-list.html');
                    }, 2000);
                } else {
                    cloud.msg('请填写完整再提交！', '50%', 2000);
                }
            });
        }
    };
    var page = new Page();
})(jQuery, window, document)
