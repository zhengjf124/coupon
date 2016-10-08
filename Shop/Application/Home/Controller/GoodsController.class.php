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
        $parameters = $this->_createParameters();
        $cat_id = intval($parameters['cat_id']);
        if (!preg_match('/^[1-9][0-9]*$/', $cat_id)) {
            $this->_returnError('1', '分类ID不正确');
        }
        $data['type_list'] = M('goods_category')->where(['parent_id' => $cat_id])->field('id as cat_id,type_name,type_img')->order('sort_order')->select();
        $this->_returnData($data);
    }

}//end