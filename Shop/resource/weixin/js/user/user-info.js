;
(function($, window, document, undefined) {
    var Page = function() {
        this.init();
    };
    Page.prototype = {
        params: {
            nick_name: '',
            sex: '',
            birthday: '',
            poss_port: '',
            temp: ''
        },
        init: function() {
            this.getData();
            this.initEvent();
            this.upLoadImg();
        },
        getData: function() {
            Pub.post('/Home/user/detail', {}, function(response) {
                if (response.error_code === 0) {
                    $("#avatar").attr("src", response.data.headimgurl);
                    $("#nickName").val(response.data.nick_name);
                    $("#sex option").each(function() {
                        if ($(this).val() == response.data.sex) {
                            $(this).prop('selected', true);
                        } else {
                            $(this).prop('selected', false);
                        }
                    });
                    $("#birthday").val(response.data.birthday);
                }
            });
        },
        initEvent: function() {
            var _this = this;
            $.Tipmsg.r = null;
            $('#info-form').Validform({
                tiptype: function(msg) {
                    layer.msg(msg);
                }
            });
            $('#submit_info').on('click', function() {
                _this.postData();
                return false;
            })
        },
        postData: function() {
            var _this = this;
            _this.params.nick_name = $("#nickName").val();
            _this.params.sex = $("#sex").val();
            _this.params.birthday = $("#birthday").val();
            if (_this.params.nick_name == "" || _this.params.sex == "" || _this.params.birthday == "") {
                layer.msg(msg);
            } else {
                Pub.post('/Home/user/update', {
                    "nick_name": _this.params.nick_name,
                    "sex": _this.params.sex,
                    "birthday": _this.params.birthday
                }, function(response) {
                    if (response.error_code === 0) {
                        cloud.msg('恭喜您，修改成功！', '50%', 2000);
                        setTimeout("location.href='" + Pub.getHtmlUrl('html/user/user-center.html') + "';", 2000);
                    }
                });
            }

        },
        upLoadImg: function() {
        	var _this = this;
        	_this.params.poss_port = Pub.getSession("_passport");
            $('#upload').Huploadify({
                auto: true,
                fileTypeExts: '*.jpg;*.png;*.jpeg;*.gif;*.bmp',
                multi: false,
                formData: {
                    key: 123456,
                    _passport: _this.params.poss_port
                },
                fileSizeLimit: 9999,
                fileObjName: 'head_img_file',
                buttonText: '', //上传按钮上的文字
                itemTemplate: _this.params.temp,
                showUploadedPercent: true, //是否实时显示上传的百分比，如20%
                showUploadedSize: true,
                removeTimeout: 9999999,
                uploader: 'http://gwshop.usrboot.com/Home/user/uploadHeadImg',
                onUploadStart: function() {
                    //alert('开始上传');
                },
                onInit: function() {
                    //alert('初始化');
                },
                onUploadSuccess: function(file, data, response) {
                    //alert('The file ' + file.name + ' was successfully uploaded with a response of ' + response + ':' + data);
                    data = jQuery.parseJSON(data);
                    $("#avatar").attr('src', data.data.imgUrl[0]);

                },
                onUploadComplete: function(file) {
                    //alert('上传完成');

                },
                onDelete: function(file) {
                    //				console.log('删除的文件：' + file);
                    //				console.log(file);

                }
            });

        }
    };
    var page = new Page();
})(jQuery)


