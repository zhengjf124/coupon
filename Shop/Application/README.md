文档说明                         {#mainpage}
============


基础说明
------------

基本约定，接口使用http协议get/post请求，返回数据均使用JSON格式返回。

JSON基本结构：

{“error_code”:0,"message":"","data":{"token":"xxxxxxxx"}}

请求成功：

- error_code 错误代码，0表示请求成功无错误。
- message: 错误信息
- data:请求成功能返回的数据，无数据无此节点。

请求失败：

- error_code 错误代码。
- message    错误信息。
- url        跳转的接口。(当错误代码 error_code=(10000,10001) 时才有该返回值 )


线上接口域名：http://xxx.com/

测试接口域名：http://xxx.gnwai.com/

shop 测试接口：http://gwshop.usrboot.com/

全局参数：
- _app       应用名称
- _platform  设备平台:ios/android/web/weixin
- _passport  用户票据

全局参数请所有接口都必传





接口目录
------------
 [首页](classHome_1_1Controller_1_1IndexController.html)

 [购物车接口](classHome_1_1Controller_1_1CartController.html)

 [我的收藏接口](classHome_1_1Controller_1_1CollectController.html)

 [会员中心](classHome_1_1Controller_1_1UserController.html)

 [会员登入/注册/判断用户是否登入](classHome_1_1Controller_1_1LoginController.html)

 [商品](classHome_1_1Controller_1_1GoodsController.html)

 [商品分类](classHome_1_1Controller_1_1GoodsCategoryController.html)

 [收货地址](classHome_1_1Controller_1_1UserAddressController.html)

 [订单接口](classHome_1_1Controller_1_1OrderController.html)

 [支付接口](classHome_1_1Controller_1_1PayController.html)

 [优惠促销消息接口](classHome_1_1Controller_1_1NoticeOrderController.html)

 [客服助手接口](classHome_1_1Controller_1_1NoticeAssistantController.html)

 [商品评价接口](classHome_1_1Controller_1_1CommentsController.html)

 [积分接口](classHome_1_1Controller_1_1CreditController.html)

 [优惠券](classHome_1_1Controller_1_1CouponsController.html)

 [文章中心](classHome_1_1Controller_1_1ArticleController.html)





本地测试环境
-----------------
   ###php环境要求：  php5.4以上版本，mysql 建议5.5以上   \n
   1、数据库配置文件

   - Shop\Application\Common\Conf\config.php
   - 'DB_NAME' => 'gwshop', //数据库名
   - 'DB_USER' => 'root',  //数据库用户名
   - 'DB_PWD' => 'root',   //数据库密码

   2、需要启用的php扩展
   - extension=php_bz2.dll
   - extension=php_curl.dll
   - extension=php_gd2.dll
   - extension=php_mbstring.dll
   - extension=php_mysql.dll
   - extension=php_mysqli.dll
   - extension=php_pdo_mysql.dll
   - extension=php_pdo_odbc.dll
   - extension=php_pdo_sqlite.dll
   - extension=php_sockets.dll
   - extension=php_sqlite3.dll
   - extension=php_xmlrpc.dll
   - extension=php_xsl.dll

   3、需开启apache 伪静态支持
   - LoadModule rewrite_module modules/mod_rewrite.so
   - AllowOverride All







