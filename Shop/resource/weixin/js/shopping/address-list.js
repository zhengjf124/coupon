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
            Pub.post('/Home/userAddress/index', {}, function(response) {
                if (response.error_code === 0) {
                    _this.addressList(response.data.user_address);
                    _this.initEvent();
                    
                }
            });
        },
        addressList: function(data) {
            $("#address-list").empty();
            var html = '';
            if (data && data.length > 0) {
                for (var i in data) {
                    html += '<li>' +
                        '<div class="address-cont" data-index="' + data[i].id + '">' +
                        '<h3><span>' + data[i].consignee + '</span><span>' + data[i].mobile + '</span></h3>' +
                        '<h4>' + data[i].province + '&nbsp;' + data[i].city + '&nbsp;' + data[i].district + '&nbsp;' + data[i].address + '</h4>' +
                        '</div>' +
                        '<i class="address-edit" data-index="' + data[i].id + '">&#xe6af;</i>' +
                        '<i class="address-delect" data-index="' + data[i].id + '">&#xe6c5;</i>' +
                        '</li>';
                }
                $("#address-list").append(html);
            } else {
                $("#address-list").append('<div class="nothing">您还没有添加收货地址，请您添加！</div>');
            }
        },
        initEvent: function() {
            var _this = this;
            $('#to-add-address').on('click', function() {
                var url = Pub.getHtmlUrl('html/shopping/address.html');
                location.href = url;
            });
            $('.address-delect').on("click", function() {
                var index = $(this).attr('data-index');
                var _self = this;
                cloud.asked('您确定要删除吗？', function() {
                    _this.delectAddress(index);
                    $(_self).parent().fadeOut();
                }, function() {
                    cloud.msg('您选择“取消”了!', '50%', 2000);
                });
            });
            $('.address-edit').on('click',function(){
            	var index = $(this).attr('data-index');
            	_this.editAddress(index);
            });
            $('.address-cont').on('click',function(){
                var index = $(this).attr('data-index');
                _this.chooleAddress(index);
            })
        },
        delectAddress: function(id) {
            Pub.post('/Home/UserAddress/delete', { id: id }, function(response) {
                if (response.error_code === 0) {
                    cloud.explain('恭喜您，删除成功！', 'success', 2000);
                }
            });
        },
        editAddress: function(id){
         	var url = Pub.getHtmlUrl('html/shopping/address-edit.html') + '?id='+id;
         	location.href = url;
        },
        chooleAddress: function(id) {
            console.log(localStorage.balanceUrl);
            if (localStorage.balanceUrl === undefined) {
                return;
            } else {
                var url = localStorage.getItem("balanceUrl");
                localStorage.removeItem('balanceUrl');
                localStorage.setItem('addressId', id); //把成功添加的地址ID返回		
                location.href = Pub.getHtmlUrl(url);
            }
        }
    };
    var page = new Page();
})(jQuery, window, document)
