<?php

namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 商品接口
 * @author xiaoxu
 * @package Home\Controller
 */
class GoodsController extends ApiController
{

    /**
     * 我是构造函数
     *
     */
    public function __construct()
    {
        parent::__construct();

    }


    /**
     * 分类列表接口 \n
     * URI : /home/goods/categoryList
     * @param :
     *     name   | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters：
     *   name   | type  |  null | description
     * ---------|-------|-------|-------------
     *  cat_id  |  int  |  必填  | 分类id
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     * type_list|  array  | 分类列表二维数组
     *
     * list :
     *    name   |  type  | description
     * ----------|--------|----------------------
     *   cat_id  |  int   |  分类ID
     * type_name | string | 分类名称
     * type_img  | string | 分类图片
     *
     */
    public function categoryList()
    {
        $cat_id = intval($this->_parameters['cat_id']);
        if (!preg_match('/^[1-9][0-9]*$/', $cat_id)) {
            $this->_returnError('1', '分类ID不正确');
        }
        $list = M('goods_category')->where(['parent_id' => $cat_id, 'is_show' => 1])->field('id as cat_id,type_name,type_img')->order('sort_order,is_hot desc')->select();
        if (!is_array($list)) {
            $list = array();
        }
        $this->_returnData(['type_list' => $list]);
    }


    /**
     * 获取排序列表 \n
     * URI : /home/goods/getSortList
     * @param :
     *     name   | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters：
     *   name   | type  |  null | description
     * ---------|-------|-------|-------------
     *   -----  |  ---  |  ---  |    无
     *
     * @return
     *   name |  type   | description
     * -------|---------|----------------------
     *   list |  array  | 排序列表二维数组
     *
     * list :
     *    name   |  type  | description
     * ----------|--------|----------------------
     *  sort_id  |  int   |  排序ID
     * sort_name | string |  排序名称
     *
     */
    public function getSortList()
    {
        $data['list'][0]['sort_id'] = 1;
        $data['list'][0]['sort_name'] = '智能排序';
        $data['list'][1]['sort_id'] = 2;
        $data['list'][1]['sort_name'] = '离我最近';
        $data['list'][2]['sort_id'] = 3;
        $data['list'][2]['sort_name'] = '人气最高';
        $data['list'][3]['sort_id'] = 4;
        $data['list'][3]['sort_name'] = '评价最好';
        $data['list'][4]['sort_id'] = 5;
        $data['list'][4]['sort_name'] = '价格最高';
        $data['list'][5]['sort_id'] = 6;
        $data['list'][5]['sort_name'] = '价格最低';
        $this->_returnData($data);
    }


}//end
