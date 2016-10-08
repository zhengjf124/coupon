<?php
namespace User\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

use Home\Logic\DemoLogic;

/**
 * pc首页
 * @author wubuze <wubuze@qq.com>
 * @package Home\Controller
 */

class IndexController extends ApiController
{ // 接口必须继承这个控制器

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();

    }



    /**
     * pc首页接口 \n
     * URI : /Home/index/index
     * @param :
     *
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * -------  | -----    | 不必填 | ---
     *
     * @return
     * list:
     *  name   | type     | description
     * --------|----------|----------------------
     * adv     | array    | 轮播图
     * cate    | array    | 分类
     * buy_goods| array   | 限时商品  buy_goods[limit_time] 倒计时
     * hot_goods| array   | 热卖商品
     * recommend| array   | 推荐
     *
     * adv:
     *  name   | type     | description
     * --------|----------|----------------------
     * ad_name | string   | 轮播图名称
     * ad_link | string   | 跳转链接
     * ad_code | string   | 图片
     *
     * cate:
     *  name   | type     | description
     * --------|----------|----------------------
     * type_name| string | 分类名称
     * type_img | string   | 图片
     * id       | int      | 分类id
     *
     * buy_goods[list]:
     *  name   | type     | description
     * --------|----------|----------------------
     * goods_name| string | 分类名称
     * goods_img | string   | 图片
     * id       | int      | 分类id
     * goods_price| decimal      | 售价
     * market_price| decimal     | 市场价
     *
     * hot_goods:
     *  name   | type     | description
     * --------|----------|----------------------
     * goods_name| string | 分类名称
     * goods_img | string   | 图片
     * id       | int      | 分类id
     * goods_price| decimal | 售价
     * goods_brief| string  | 描述
     *
     * recommend:
     *  name   | type     | description
     * --------|----------|----------------------
     * goods_name| string | 分类名称
     * goods_img | string   | 图片
     * id       | int      | 分类id
     * goods_price| decimal | 售价
     * goods_brief| string  | 描述
     * click_count| int     | 点击量
     * favorite_count| int  | 收藏数量
     * comment_count| int   | 评论数量
     *
     * @note 此方法请使用post提交 文档使用Doxyfile自动生成
     */
    public function index()
    {

        // 获取http输入参数统一使用下面方法，此方法兼容 get和post的数据
        //$name = I('name', '');
        // 参数验证

        //获取轮播图
        $adv = M('ad')->where(['pid'=>1])->field('ad_name,ad_link,ad_code')->select();
        //分类
        $cate = M('goodsCategory')->where('level=1')->field('type_name,type_img,id')->order('sort_order')->limit(4)->select();
        //首页推荐商品 3
        $recommend = M('goods')->field('id,goods_img,goods_price,goods_name,goods_brief,click_count,favorite_count,comment_count')->where("is_recommend=1 and is_on_sale=1")->order('id DESC')->limit(3)->select();
        //限时抢购 3个
        $buy_goods['list'] = M('goods')->field('id,goods_img,goods_price,goods_name,market_price')->where("gtype=2 and is_on_sale=1")->order('id DESC')->limit(3)->select();
        $end_time = M('active')->where(['type'=>'limit_goods'])->getField('end_time');
       // $buy_goods['limit_time'] = date('Y/m/d H:i:s',$end_time);
        $buy_goods['limit_time'] = $end_time;
        //热卖单品 8
        $hot_goods = M('goods')->field('id,goods_img,goods_price,goods_name,goods_brief,click_count')->where("is_hot=1 and is_on_sale=1")->order('id DESC')->limit(8)->select();


        // 业务逻辑
        $data = [];
//        $data['adv'] = $adv;
//        $data['cate'] = $cate;
//        $data['buy_goods'] = $buy_goods;
//        $data['hot_goods'] = $hot_goods;
//        $data['recommend']  = $recommend;
        $data['aa'] = 'ok';
        // 返回数据格式化，返回数据里不要出现null
        $this->_returnData($data);

    }











}