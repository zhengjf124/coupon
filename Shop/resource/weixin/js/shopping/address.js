;
(function($, window, document, undefined) {
    var Page = function() {
        this.init();
    };
    Page.prototype = {
        init: function() {
        	this.initEvent();
        },
        initEvent: function() {
        	var _this = this;
            $.Tipmsg.r = null;
            $("#address-form").Validform({
                tiptype: function(msg) {
                    layer.msg(msg);
                }
            });
            $('#submit_address').on('click',function(){
            	_this.postData();
            	return false;
            });
        },
        postData: function() {
            var consignee = $("#consignee").val();
            var mobile = $("#mobile").val();
            var province = $("#province").val();
            var city = $("#city").val();
            var district = $("#district").val();
            var address = $("#address").val();
            if (consignee === "" || mobile === "" || province === "" || city === "" || district === "" || address === "") {
                layer.msg(msg);
            } else {
                Pub.post('/Home/UserAddress/add', {
                    "consignee": consignee,
                    "mobile": mobile,
                    "province": province,
                    "city": city,
                    "district": district,
                    "address": address
                }, function(response) {
                    if (response.error_code === 0) {
                    	cloud.msg('恭喜您，添加成功！','50%',2000);
                        if (localStorage.balanceUrl === undefined) { //添加地址不是从订单确认页过来	
                            setTimeout(function() {
                            location.href = Pub.getHtmlUrl('html/shopping/address-list.html');
                            }, 2000);                   
                        } else { //添加地址从订单确认页过来
                            var url = localStorage.getItem("balanceUrl");
                            localStorage.removeItem('balanceUrl');
                            localStorage.setItem('addressId', response.data.id); //把成功添加的地址ID返回						
                            location.href = Pub.getHtmlUrl(url);
                        }

                    }
                })
            }

        }
    };
    var page = new Page();
})(jQuery, window, document)
