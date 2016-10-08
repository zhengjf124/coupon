<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
header("Content-type: text/html; charset=utf-8");
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);
define('BUILD_DIR_SECURE', false);
define('__PREFIX__','gwshop_');
define('AUTH_KEY','coupon1238');//验证字接口符串

// 定义应用目录
define('APP_PATH',realpath('..') . '/Application/');
define('THINK_PATH',realpath('../../ThinkPHP').'/');


define('UPLOAD_PATH','Public/upload/'); // 编辑器图片上传路径
define('TPSHOP_CACHE_TIME',86400); // TPshop 缓存时间
define('SITE_URL','http://'.$_SERVER['HTTP_HOST']); // 网站域名


// 引入ThinkPHP入口文件
require THINK_PATH . 'ThinkPHP.php';
