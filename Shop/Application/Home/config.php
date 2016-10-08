<?php
return array(
    /* 加载公共函数 */
    'LOAD_EXT_FILE' => 'common',
    //'配置项'=>'配置值'
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_NAME' => 'gwshop',
    'DB_USER' => 'demo',
    'DB_PWD' => 'demo123!@#',
    'DB_PREFIX' => 'gwshop_',

    'site_url' => SITE_URL,//网站域名
    'ORDER_STATUS' => array(
        100 => '待付款',
        200 => '待发货',
        101 => '已取消',
        210 => '待收货',
        220 => '待评价',
        222 => '交易结束',
        300 => '退款中',
        310 => '退款成功',
    ),
    'SHIPPING_STATUS' => array(
        0 => '未发货',
        1 => '已发货',
        2 => '部分发货'
    ),
    'PAY_STATUS' => array(
        0 => '未支付',
        1 => '已支付'
    ),
    'COUPON_TYPE' => array(
        0 => '面额模板',
        1 => '按用户发放',
        2 => '注册发放',
        3 => '邀请发放',
        4 => '线下发放'
    ),
);