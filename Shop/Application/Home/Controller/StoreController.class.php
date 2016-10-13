<?php

namespace Home\Controller;

require_once(APP_PATH . 'ApiController.class.php');

use Application\ApiController;

class StoreController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 获取商家列表 \n
     * URI : /home/store/storeLost
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *   city_id  |  int   | 必须  | 城市ID
     *    page    |  int   | 必须  | 页码
     *   area_id  |  int   | 可选  | 商圈ID
     *   cat_id   |  int   | 可选  | 分类ID
     *   sort_id  |  int   | 可选  | 排序ID
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     *   list   |  array  |  城市列表
     *
     * list :
     *      name    |  type  | description
     * -------------|--------|----------------------
     *    store_id  |  int   |  商家ID
     *   store_name | string |  商家名称
     * comment_count| string |  评论次数
     *     label    | string |  标签
     *   keywords   | string |  关键字
     *   avg_price  | float  |  人均消费
     *   store_img  | string |  列表引导图
     * comment_level|  int   |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *   distance   | string |  距离
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/store/storeLost/parameters/%7b%22city_id%22%3a%22350100%22%2c%22page%22%3a%221%22%7d
     */
    public function storeLost()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['city_id'])) {
            $this->_returnError(10020, '城市ID不合法');
        }

        $nowPage = $this->_parameters['page'];//当前页码
        if (!preg_match('/^[1-9][0-9]*$/', $nowPage)) {
            $this->_returnError(10040, '页码不合法');
        }

        if (preg_match('/^[1-9][0-9]*$/', $this->_parameters['cat_id'])) {
            $cat_ids = M('goods_category')->where(['parent_id' => $this->_parameters['cat_id'], 'is_show' => 1])->getField('id', true);
            $cat_ids[] = $this->_parameters['cat_id'];
            $where['cat_id'] = array('in', $cat_ids);
        }

        if (preg_match('/^[1-9][0-9]*$/', $this->_parameters['area_id'])) {
            $where['area_id'] = $this->_parameters['area_id'];
        } else {
            $where['city_id'] = $this->_parameters['city_id'];
        }

        /*        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['sort_id'])) {
                    $this->_returnError(10042, '排序ID不合法');
                }*/
        //排序未完成
        /*        switch ($this->_parameters['sort_id']) {
                    case 2:

                        break;
                }*/

        $where['is_show'] = 1;

        $listRows = 1;//一页的条数
        $totalRows = M('store')->where($where)->count();//总条数
        $totalPages = ceil($totalRows / $listRows);//总页数
        if ($nowPage > $totalPages) {
            $this->_returnError(10041, '页码超过了总页数');
        }

        $firstRow = $listRows * ($nowPage - 1);//从第几条开始查询

        $list = M('store')->where($where)->field('store_id,store_name,comment_count,label,keywords,avg_price,store_img')->order('sort_order')->limit($firstRow, $listRows)->select();
        $data['next_page'] = $nowPage + 1;
        $data['total_page'] = $totalPages;
        if (!is_array($list) || empty($list)) {
            $list = array();
        } else {
            foreach ($list as &$value) {
                $value['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                $value['distance'] = '<500m';//距离
            }
        }
        $data['list'] = $list;
        $this->_returnData($data);
    }

    /**
     * 获取商家详细信息 \n
     * URI : /home/store/detail
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  store_id  |  int   | 必须  | 商家ID
     *
     * @return
     *      name    |  type  | description
     * -------------|--------|----------------------
     *  store_detail|  array |  商家详情
     *  coupon_list |  array |  优惠券列表
     *
     * store_detail:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *    store_id  |  int   |  商家ID
     *   store_name | string |  商家名称
     * comment_count| string |  评论次数
     *     label    | string |  标签
     *   keywords   | string |  关键字
     *   avg_price  | float  |  人均消费
     *   store_img  | string |  详情页图片
     *  store_phone | string |  详情页图片
     *    address   | string |  地址
     * comment_level|  int   |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *   distance   | string |  距离
     *
     * coupon_list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   coupon_id  |  int   |  优惠券ID
     *  coupon_name | string |  优惠券名称
     *  coupon_price| float  |  优惠券价格
     *  market_price| float  |  原价
     *  coupon_sales|  int   |  销量
     *   coupon_img |  int   |  列表引导图
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/store/detail/parameters/%7B%22store_id%22:%2223%22%7D
     *
     */
    public function detail()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['store_id'])) {
            $this->_returnError(10042, '商家ID不合法');
        }
        $store_detail = M('store')->where(array('store_id' => $this->_parameters['store_id']))->field('store_id,store_name,comment_count,label,keywords,avg_price,store_banner,store_phone,address')->find();
        if (!$store_detail || !is_array($store_detail)) {
            $this->_returnError(10043, '商家不存在');
        }
        $store_detail['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
        $store_detail['distance'] = '<500m';//距离

        $coupon_ids = M('coupons_store')->where(array('store_id' => $store_detail['store_id']))->getField('coupon_id', true);
        if ($coupon_ids) {
            $coupon_list = M('coupons_sale')->where(array('coupon_id' => array('in', $coupon_ids)))->field('coupon_id,coupon_name,coupon_price,market_price,coupon_sales,coupon_img')->order('sort_order')->select();
        }
        if (!isset($coupon_list)) {
            $coupon_list = array();
        }
        $this->_returnData(['store_detail' => $store_detail, 'coupon_list' => $coupon_list]);
    }
}