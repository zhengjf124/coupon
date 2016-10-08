/* 
 * 公共方法,建议涉及到一些公用方法可以再里面扩展
 */

//防止ie8报错
window.console = window.console || (function () {
    var c = {};
    c.log = c.warn = c.debug = c.info = c.error = c.time = c.dir = c.profile = c.clear = c.exception = c.trace = c.assert = function () {};
    return c;
})();

+ function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS之类的
        module.exports = factory(require('jquery'));
    } else {
        // 浏览器全局变量(root 即 window)
        var obj = factory(jQuery);
        root.Pub = obj;
    }
}(this, function ($) {
    var Pub = {
        debug: true, //开启调试开关
        
        clientRoot: "/weixin/",    //前端代码根路径

        /**
         * 对jquery的post进一步封装，默认会注入_app\_platform\_passport,
         * 同时对返回数据默认进行通用错误处理,未登录会进行登录处理
         * @param {string} url 请求地址
         * @param {object} data 请求附加参数
         * @param {function} callback 异步回调函数,默认已对数据进行通用错误处理
         * @param {string} type 返回内容类型
         * 
         * @returns {object}  返回Deferred对象，支持promise
         */
        post: function (url, data, callback, type) {
            return this._ajax(url, data, callback, type);
        },
        /**
         * 对jquery的post进一步封装，默认会注入_app\_platform\_passport,
         * 同时对返回数据默认进行通用错误处理,未登录会进行登录处理
         * @param {string} url 请求地址
         * @param {object} data 请求附加参数
         * @param {function} callback 异步回调函数,默认已对数据进行通用错误处理
         * @param {string} type 返回内容类型
         * 
         * @returns {object}  返回Deferred对象，支持promise
         */
        get: function (url, data, callback, type) {
            return this._ajax(url, data, callback, type, "get");
        },
        /**
         *搜索ID或名字等，通过关键词获取的 
         */
        searchToObject: function () {
            var pairs = window.location.search.substring(1).split("&"),
                    obj = {},
                    pair,
                    i;
            for (i in pairs) {
                if (pairs[i] === "")
                    continue;

                pair = pairs[i].split("=");
                obj[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
            }
            return obj;
        },
        /**
         *倒计时方法 
         */
        countDown: function (time, id) {
            var dayElem = $(id).find('.day');
            var hourElem = $(id).find('.hour');
            var minuteElem = $(id).find('.minute');
            var secondElem = $(id).find('.second');
            var endTime = new Date(time).getTime(), //月份是实际月份-1
                    sysSecond = (endTime - new Date().getTime()) / 1000;
            var timer = setInterval(function () {
                if (sysSecond > 1) {
                    sysSecond -= 1;
                    var day = Math.floor((sysSecond / 3600) / 24);
                    var hour = Math.floor((sysSecond / 3600) % 24);
                    var minute = Math.floor((sysSecond / 60) % 60);
                    var second = Math.floor(sysSecond % 60);
                    dayElem && $(dayElem).text(day); //计算天
                    $(hourElem).text(hour < 10 ? "0" + hour : hour); //计算小时
                    $(minuteElem).text(minute < 10 ? "0" + minute : minute); //计算分钟
                    $(secondElem).text(second < 10 ? "0" + second : second); //计算秒杀
                } else {
                    clearInterval(timer);
                }
            }, 1000);
        },
        /**
         * 私有方法不要调用
         */
        _ajax: function (url, data, callback, type, method) {
            var self = this,
                    filterCallback;
            if ($.isFunction(data)) {
                type = type || callback;
                callback = data;
                data = undefined;
            }

            url = this.getAbsoluteUrl(url);

            if (url.indexOf("_passport") === -1 && ($.isPlainObject(data) && !data.entriedCode)) {
                data = $.extend(this.getAuthInfo(), data);
            } else if (url.indexOf("_passport") === -1 && data === undefined) {
                data = this.getAuthInfo();
            }

            //回调函数注入处理,通用错误处理
            if ($.isFunction(callback)) {
                filterCallback = function (data, textStatus, jqXHR) {
                    data = self.catchCommonError(data);
                    callback(data, textStatus, jqXHR);
                };
            }

            var deferred = new $.Deferred();

            var xhr = method === "get" ? $.get(url, data, filterCallback, type) : $.post(url, data, filterCallback, type);

            xhr.then(
                    function (data, textStatus, jqXHR) { //成功回调
                        data = self.catchCommonError(data);
                        deferred.resolveWith(this, [data, textStatus, jqXHR]);
                    },
                    function (XMLHttpRequest, textStatus, errorThrown) { //失败回调
                        deferred.rejectWith(this, [XMLHttpRequest, textStatus, errorThrown]);
                    }
            );

            var promise = deferred.promise();

            promise.abort = function () {
                xhr.abort();
                deferred.reject("abort");
            };

            return promise;
        },
        /**
         * 从返回接口的数据是否含有错误
         * @param {object} data 待处理数据
         * @returns {boolean} true/false
         */
        hasError: function (data) {
            if ($.isPlainObject(data) && data.code) {
                var code = data.code;
                if (code !== "0") {
                    return false;
                }
            }
            return true;
        },
        /**
         * 获取绝对路径，当页面需要进行跨模块调用时有用
         * @param {type} url
         * @returns {undefined}
         */
        getAbsoluteUrl: function (url) {
            return url;
        },
        /**
         * 获取认证信息
         * @returns {undefined}
         */
        getAuthInfo: function () {
            var _passport = this.getSession("_passport");
            var res = {
                _app: "shop",
                _platform: "weixin"
            };
            if (_passport) {
                res._passport = _passport;
            }
            return res;
        },
        /**
         * 设置认证信息
         * @param {object} data
         * @returns {undefined}
         */
        setAuthInfo: function (data) {
            var _passport = data && data._passport;
            this.setSession("_passport", _passport);
        },
        /**
         * 清楚认证信息
         * @returns {undefined}
         */
        clearAuthInfo: function () {
            this.delSession("_passport");
        },
        /**
         * 所有接口都有通用错误处理
         * @param {object} data
         * @returns {object}
         */
        catchCommonError: function (data) {
            if (!$.isPlainObject(data) || !data.error_code) { //错误数据格式
                return data;
            }
            var code = data.error_code,
                    message = data.message;
            switch (code) {
                case "10000": //未登录
                    this._noLogin(data);
                    break;
                case "10001": //未登录
                    this._noLogin(data);
                    break;
            }
            return data;
        },
        /**
         * 解析url地址
         * @param {string} url 等待解析url地址
         * @return {object} 
         */
        parseUrl: function (url) {
            var urlRegExp = new RegExp(/^([^:]+):\/\/(?:([^:@]+)(?::([^@]+))?@)?([^:\/]+)(?:\:([0-9]*))?([^#\?]+)?(?:\?([^#]+))?(#.+)?$/ig); // :RegExp
            var pathRegExp = new RegExp('([^/]+)', 'g'); // :RegExp
            var queryRegExp = new RegExp('([^=&]+)=([^&]+)', 'g'); // :RegExp
            var info = {
                url: url
            };
            var match = urlRegExp.exec(url);
            if (match !== null) {
                info.protocol = match[1] || ''; // 'https'
                info.user = match[2] || ''; // 'admin'
                info.password = match[3] || ''; // '123456'
                info.subdomain = match[4] || ''; // 'secure'
                info.domain = match[4] || ''; // 'example.com'
                info.port = match[5] || ''; // '8181'
                info.path = match[6] || ''; // '/local/test'
                info.query = match[7] || ''; // 'search=bk&show=page'
                info.hash = match[8] || ''; // '#content'
                var path = info.path.match(pathRegExp);
                info.parsePath = (path === null) ? [] : path;
                var parseQuery = {};
                info.query.replace(queryRegExp, function (match, c1, c2) {
                    parseQuery[c1] = c2;
                    return match;
                });
                info.parseQuery = parseQuery;
                return info;
            }
        },
        /**
         * 拼接url地址
         * @param {string} url url地址
         * @param {object} param url参数
         * @returns {string} 拼接后的url地址
         * 
         */
        getUrl: function (url, param) {
            if (url.indexOf("?") !== -1) {
                url += "&" + $.param(param);
            } else {
                url += "?" + $.param(param);
            }
            return url;
        },
        /**
         * 返回当前url地址中的参数
         * @returns {object}
         */
        getUrlParam: function () {
            var urlInfo = this.parseUrl(window.location.href);
            return urlInfo && urlInfo.parseQuery;
        },
        /**
         * 获取会话中指定的值
         * @param {string} key 要获取的key
         * @return {stirng} 返回指定值
         */
        getSession: function (key) {
            if (sessionStorage) {
                return sessionStorage.getItem(key);
            } else {
                return $.cookie(key);
            }
        },
        /**
         * 设置会话指定值
         * @param {string} key 会话的key
         * @param {string} val 要设置的值
         * @returns {void}
         */
        setSession: function (key, val) {
            if (sessionStorage) {
                sessionStorage.setItem(key, val);
            } else {
                $.cookie(key, val, {
                    "path": "/"
                });
            }
        },
        /**
         *  删除指定会话值
         * @param {type} key
         * @returns {undefined}
         */
        delSession: function (key) {
            if (sessionStorage) {
                sessionStorage.removeItem(key);
            } else {
                $.removeCookie(key, {
                    "path": "/"
                });
            }
        },
        /**
         * 获取html文件url
         */
        getHtmlUrl: function(htmlFile){
            return this.clientRoot + htmlFile;
        },
        /**
         * 监测当前页面是否已经登录,用户未登录需要跳转到授权页
         * @returns {undefined}
         */
        _checkPageAuth: function () {
            var self = this;
            var hasHidden = $("body").hasClass("hidden");
            if (!hasHidden) {
                return true;
            }
            var _passport = this.getSession("_passport");
            var data = {
                _app: "shop",
                _platform: "weixin",
                _passport: _passport
            };
            var url = "/Home/Login/check";
            $.ajax({
                async: false,
                data: data,
                url: this.getAbsoluteUrl(url)
            }).then(function (result) {
                self.catchCommonError(result);
            });
        },
        /**
         * 未登录处理
         * @param {object} data 获取到的数据
         * @returns {void}
         */
        _noLogin: function (data) {
            var url = data.url;
            url += window.encodeURI(window.location.href);
            window.location.href = url;
        },
        /**
         * 设置登录相关的数据
         * @returns {undefined}
         */
        _setLoginData: function () {
            var href = window.location.href;
            var param = this.getUrlParam(href);
            if (param && param._passport) {
                this.setAuthInfo(param);
            }
        }

    };

    Pub._setLoginData();
    Pub._checkPageAuth();

    return Pub;
});