<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 首页
 * @author xiaoxu <997998478@qq.com>
 * @package Home\Controller
 */
class IndexController extends ApiController
{ // 接口必须继承这个控制器

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();

    }


    /*    public function test()
        {
            //$a = M('goods')->alias('g')->join('gwshop_goods_category t ON t.id= g.cat_id')->field('g.id,g.cat_id,g.goods_name,t.type_name')->select();
            //$a = M()->table('gwshop_goods g,gwshop_goods_category t')->field('g.id,g.cat_id,g.goods_name,t.type_name')->where('t.id= g.cat_id')->select();
            $a = M('goods')->field('goods_name,max(sort_order) as sort_order')->group('cat_id')->having('count(id) < 3')->select();
            //echo $a = M('goods')->getLastSql();
            echo json_encode($a);
        }*/


    /**
     * 首页接口 \n
     * URI : /Home/index/index
     * @param :
     *     name   | type   | null| description
     * -----------|--------|-----|-------------
     * -------  | -----    | 不必填 | ---
     *
     * @return
     * type_list:
     *    name  |  type  | description
     * ---------|--------|----------------------
     *  type_id |  int   | 分类ID
     * type_name| string | 分类名称
     *  type_img| string | 分类图片
     *
     *
     * @note 此方法请使用post提交 文档使用Doxyfile自动生成
     */
    public function index()
    {
        $data['type_list'] = M('goods_category')->where(['parent_id' => 0])->field('id as cat_id,type_name,type_img')->order('sort_order')->select();
        $this->_returnData($data);
    }


}