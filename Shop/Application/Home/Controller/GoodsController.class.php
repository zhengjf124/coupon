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
     * 一级分类列表接口 \n
     * URI : /home/goods/onceCategoryList
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
    public function onceCategoryList()
    {
        $list = M('goods_category')->where(['parent_id' => 0, 'is_show' => 1])->field('id as cat_id,type_name,type_img')->order('sort_order,is_hot desc')->select();
        if ($list) {
            foreach ($list as &$value) {
                $value['category_num'] = M('goods_category')->where(array('parent_id' => $value['cat_id'], 'is_show' => 1))->count();
            }
        } else {
            $list = array();
        }
        $this->_returnData(['type_list' => $list]);
    }

    /**
     * 二级分类列表接口 \n
     * URI : /home/goods/secondCategoryList
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
    public function secondCategoryList()
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
        $this->_returnData($this->sortList());
    }


}//end
