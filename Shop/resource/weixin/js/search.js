;
(function($, window, document, undefined) {
    var Page = function() {
        this.init();
    };
    Page.prototype = {
        params: {
            searchWord: '',
            url: '',
            canAdd: '',
            hisArt: '',
            len: 0,
            json: []
        },
        init: function() {
            this.initParams();
            this.addHis();
            this.getHis();
            this.initEvent();
        },
        initParams: function() {
            this.params.searchWord = $("#word").val();
            this.params.canAdd = true;
            this.params.hisArt = $.cookie('hisArt');
        },
        addHis: function() {
            var _this = this;
            if (_this.params.hisArt && _this.params.hisArt != null) {
                _this.params.hisArt = eval("(" + _this.params.hisArt + ")");
                _this.params.len = _this.params.hisArt.length;
            }
            $(_this.params.hisArt).each(function() {
                if (this.word === _this.params.searchWord) {
                    _this.params.canAdd = false; //已经存在，不能插入 
                    return false;
                }
            });
            if (_this.params.canAdd === true) {
                _this.params.json = "[";
                var start = 0;
                if (_this.params.len > 12) { start = 1; }
                for (var i = start; i < _this.params.len; i++) {
                    _this.params.json = _this.params.json + "{\"word\":\"" + _this.params.hisArt[i].word + "\"},";
                }
                _this.params.json = _this.params.json + "{\"word\":\"" + _this.params.searchWord + "\"}]";
                $.cookie("hisArt", _this.params.json, { path: '/', expires: 3 });
            }
        },
        getHis: function() {
            var _this = this;
            _this.params.json = eval("(" + $.cookie("hisArt") + ")");
            if (_this.params.json.length > 0) {
                $("#search_list").empty();
                var html = "";
                for (var i = 0; i < _this.params.json.length; i++) {
                    html += '<a href="' + Pub.getHtmlUrl("html/goods/list.html") + '?keyword=' + _this.params.json[i].word + '" target="_blank">' + _this.params.json[i].word + '</a>';
                }
                $("#search_list").append(html);
            }

        },
        initEvent: function() {
            var _this = this;
            $('#word').on('input propertychange', function() {
                var word = $("#word").val();
                if (word === '') {
                    $(".clear-word").hide();
                } else {
                    $(".clear-word").show();
                };
            });
            $('.clear-word').on('click', function() {
                $("#word").val('');
                $(".clear-word").hide();
            });
            $('#submit_search').on('click', function() {
                var word = $("#word").val();
                words = word.replace(/ /g, '');
                if (words != '') {
                    _this.params.url = Pub.getHtmlUrl('html/goods/list.html') + '?keyword=' + encodeURI(_this.params.searchWord);
                    location.href = _this.params.url;
                } else {
                    cloud.explain('请输入关键词搜索！', 2000);
                }

            });
            $('#del_search').on('click', function() {
                cloud.asked('您确定要清除最近搜索记录吗？', function() {
                    $.cookie('hisArt', '' ,{path:'/',expires:-1});
                    $("#search_list").empty();
                    cloud.explain('恭喜您，清除成功！', 'success', 2000);
                    setTimeout(function() { window.location.reload(); }, 2000);
                }, function() {
                    cloud.msg('您选择了“取消”！', '50%', 2000);
                });


            })

        }
    };
    var page = new Page();
})(jQuery, window, document)
