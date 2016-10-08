文档说明                          {#接口说明！！！}
============


基础说明
------------

测试环境域名：192.168.1.100

shop基本约定，接口使用http协议get/post请求，返回数据均使用JSON格式返回。\n

所有接口都必须进行加密验证，否则无法使用接口\n
加密规则：\n
	将接口文档中参数parameters下的所有参数加上后台提供的token根据键值进行升序排列，\n
	将排列后各个健对应的值按顺序用&进行连接，再使用md5加密得到sign。\n
	parameters则需要用json格式并且将其URL编码后传给服务器，所有的接口，服务器都只接收两个参数sign和parameters\n

	token 为健  值为 coupon1238(测试环境使用)\n

例：
     parameters:
         name   |  type  | null | description
     -----------|--------|------|-------------
      password  | string | 必填  |   密码
       mobile   | string | 必填  |  手机号码      
        time    | string | 必填  |   时间

    将mobile,password,time,token进行排序(升序)\n
    结果为：
    	mobile => 13688888888\n
    	password => 123456\n
    	time => 1474941959\n
    	token => coupon1238\n
    sign = md5(13688888888&123456&1474941959&coupon1238)\n
    parameters(json) = {"mobile":"13688888888","password":"123456","time":"1474941959"}\n
    parameters(urlencode) = %7b%22mobile%22%3a%2213688888888%22%2c%22password%22%3a%22123456%22%2c%22time%22%3a%221474941959%22%7d（url编码后的结果）\n

   	之后将 sign 和 parameters 用http协议POST/GET 传给服务器即可\n
