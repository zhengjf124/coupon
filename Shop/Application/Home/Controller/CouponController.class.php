<?php

namespace Home\Controller;

require_once(APP_PATH . 'ApiController.class.php');

use Application\ApiController;

/**
 * 优惠券
 * Class CouponController
 * @package Home\Controller
 */
class CouponController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 获取优惠券详细信息 \n
     * URI : /home/coupon/detail
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters:
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  coupon_id |  int   | 必须  | 优惠券ID
     *
     * @return
     *      name    |  type  | description
     * -------------|--------|----------------------
     * coupon_detail|  array |  优惠券详情
     *  coupon_list |  array |  优惠券列表
     *
     * store_detail:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   coupon_id  |  int   |  优惠券ID
     *  coupon_name | string |  优惠券名称
     *  coupon_desc | string |  描述
     * market_price | float  |  原价
     * coupon_price | float  |  实际价格
     * coupon_sales |  int   |  销量
     * coupon_banner| string |  详情页图片
     *
     * coupon_list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   store_id   |  int   |  门店ID
     *  store_name  | string |  门店名称
     * comment_count|  int   |  评论次数
     *  store_phone | string |  联系电话
     *    address   | string |  地址
     * comment_level|  int   |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *   distance   | string |  距离
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/store/detail/parameters/%7B%22store_id%22:%223%22%7D
     *
     */
    public function detail()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['coupon_id'])) {
            $this->_returnError(10044, '优惠券ID不合法');
        }

        $coupon_detail = M('coupons_sale')->where(array('coupon_id' => $this->_parameters['coupon_id'], 'is_delete' => 0))->field('coupon_id,coupon_name,coupon_desc,market_price,coupon_price,coupon_sales,coupon_banner')->find();
        if (!$coupon_detail || !is_array($coupon_detail)) {
            $this->_returnError(10045, '优惠券不存在');
        }

        $store_ids = M('coupons_store')->where(array('coupon_id' => $coupon_detail['coupon_id']))->getField('store_id', true);
        if ($store_ids) {
            $store_list = M('store')->where(array('store_id' => array('in', $store_ids), 'is_delete' => 0))->field('store_id,store_name,comment_count,store_phone,address')->order('sort_order')->select();
            if ($store_list) {
                foreach ($store_list as &$value) {
                    $value['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                    $value['distance'] = '<500m';//距离
                }
                unset($value);
            }
        }

        if (!isset($store_list)) {
            $store_list = array();
        }

        $this->_returnData(['coupon_detail' => $coupon_detail, 'store_list' => $store_list]);
    }

}
