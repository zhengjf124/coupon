<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

use Home\Logic\DemoLogic;

/**
 * 会员接口
 * @author wubuze
 * @package Home\Controller
 */
class GoodsCategoryController extends ApiController
{

    /**
     * 我是构造函数
     *
     */
    public function __construct() {
        parent::__construct();

    }
    /**
    * 商品分类接口 \n
    * URI : /Home/goodsCategory/index
    * @param :
    * name     | type     |  null |description
    * ---------|----------|-------|-------------
    *   -      | -        |  -    |  不需要
    *
    * @return
    * name   | type     | description
    * --------|----------|----------------------
    * list     | array   | 分类列表二维数组
    * count    | int     | 一级分类总数
    * list :
    * name   | type     | description
    * -------|----------|----------------------
    * id     | int      | 分类id
    * type_name | string    | 分类标题
    * type_desc | string   | 分类描述
    * type_img | string   | 分类图片
    * parent_id | int     | 父级id
    * next     |  array    | 子级列表
    * hot_goods |  array   | 热门单品
    *
    */
    public function index(){

        $where = ['parent_id' => 0];
        $count = M('GoodsCategory')->where($where)->count();
        $list = M('GoodsCategory')->field('id,type_name,type_desc,type_img,parent_id')->where($where)->select();

        foreach($list as &$val){
            $goodsLogic = new \Home\Logic\GoodsLogic();
            $val['hot_goods'] = $goodsLogic->getHotGoods($val['id']);//热门单品
            $val['next'] = $this->nextTypeList($val['id']);
            if($val['next']){
                foreach($val['next'] as &$val2){
                    $val2['next'] = $this->nextTypeList($val2['id']);//3级分类
                }
            }
        }
        $data = ['list'=>$list, 'count'=>$count];
        $this->_returnData($data);


    }

    /**
     * 获取下一级分类
     *
     */
    private function nextTypeList($id)
    {
        $type = M('GoodsCategory')->where(['parent_id'=>$id])->field('id,type_name,type_desc,type_img,parent_id')->select();
        return $type;
    }









}//end
