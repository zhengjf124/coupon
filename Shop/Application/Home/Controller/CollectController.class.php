<?php

namespace Home\Controller;

class CollectController extends MemberController
{
    /*这是构造函数*/
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 收藏门店 \n
     * URI : /home/collect/collectStore
     * @param :
     *    name    | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters：
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *  passport | string | 必填 |  用户登录凭证
     *  store_id |  int   | 必填 |  商家ID
     *
     * @return
     *   name   |  type  | description
     * ---------|--------|--------------
     *   -----  |  ----  |  无数据
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/collect/collectStore/parameters/%7b%22passport%22%3a%22d2ab2b971ff0dc34b54c0eaa664873f0%22%2c%22store_id%22%3a%221%22%7d
     */
    public function collectStore()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['store_id'])) {
            $this->_returnError(10042, '商家ID不合法');
        }
        $store_detail = M('store')->where(array('store_id' => $this->_parameters['store_id'], 'is_delete' => 0))->field('store_id')->find();
        if (!$store_detail || !is_array($store_detail)) {
            $this->_returnError(10043, '商家不存在');
        }

        $collect_info = M('store_favorite')->where(array('user_id' => $this->user_id, 'store_id' => $this->_parameters['store_id']))->field('collect_id')->find();
        if (!$collect_info) {
            M('store_favorite')->data(['user_id' => $this->user_id, 'store_id' => $this->_parameters['store_id'], 'add_time' => $this->_now])->add();
            M('store')->where(array('store_id' => $this->_parameters['store_id']))->setInc('favorite_count');
        }
        $this->_returnData();
    }

    /**
     * 取消收藏门店 \n
     * URI : /home/collect/cancelCollectStore
     * @param :
     *    name    | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters：
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *  passport | string | 必填 |  用户登录凭证
     *  store_id |  int   | 必填 |  商家ID(json)[1,2,3,4,5,6]
     *
     * @return
     *   name   |  type  | description
     * ---------|--------|--------------
     *   -----  |  ----  |  无数据
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/collect/cancelCollectStore/parameters/%7b%22passport%22%3a%22d2ab2b971ff0dc34b54c0eaa664873f0%22%2c%22store_id%22%3a%22%5b1%5d%22%7d
     */
    public function cancelCollectStore()
    {
        $store_id = json_decode($this->_parameters['store_id'], true);
        if (!is_array($store_id)) {
            $this->_returnError(10042, '商家ID不合法');
        }

        foreach ($store_id as $value) {
            if (!preg_match('/^[1-9][0-9]*$/', $value)) {
                $this->_returnError(10042, '商家ID不合法');
            }
        }
        unset($value);

        foreach ($store_id as $value) {
            $num = M('store_favorite')->where(array('user_id' => $this->user_id, 'store_id' => $value))->delete();
            if (preg_match('/^[1-9][0-9]*$/', $num)) {
                M('store')->where(array('store_id' => $value))->setDec('favorite_count');
            }
            unset($num);
        }
        unset($value);

        $this->_returnData();
    }

    /**
     * 收藏优惠券 \n
     * URI : /home/collect/collectCoupon
     * @param :
     *    name    | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters：
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *  passport | string | 必填 |  用户登录凭证
     *  coupon_id|  int   | 必填 |  优惠券ID
     *
     * @return
     *   name   |  type  | description
     * ---------|--------|--------------
     *   -----  |  ----  |  无数据
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/collect/cancelCollectCoupon/parameters/%7B%22passport%22%3a%22d2ab2b971ff0dc34b54c0eaa664873f0%22%2c%22coupon_id%22%3a%22[1,2]%22%7D
     *
     */
    public function collectCoupon()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['coupon_id'])) {
            $this->_returnError(10044, '优惠券ID不合法');
        }

        $coupon_detail = M('coupons_sale')->where(array('coupon_id' => $this->_parameters['coupon_id'], 'is_delete' => 0))->field('coupon_id')->find();
        if (!$coupon_detail || !is_array($coupon_detail)) {
            $this->_returnError(10045, '优惠券不存在');
        }
        $collect_info = M('coupons_favorite')->where(array('user_id' => $this->user_id, 'coupon_id' => $this->_parameters['coupon_id']))->field('collect_id')->find();
        if (!$collect_info) {
            M('coupons_favorite')->data(['user_id' => $this->user_id, 'coupon_id' => $this->_parameters['coupon_id'], 'add_time' => $this->_now])->add();
            M('coupons_sale')->where(array('coupon_id' => $this->_parameters['coupon_id']))->setInc('favorite_count');
        }
        $this->_returnData();
    }

    /**
     * 取消收藏优惠券 \n
     * URI : /home/collect/cancelCollectCoupon
     * @param :
     *    name    | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters：
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *  passport | string | 必填 |  用户登录凭证
     *  coupon_id|  int   | 必填 |  优惠券ID
     *
     * @return
     *   name   |  type  | description
     * ---------|--------|--------------
     *   -----  |  ----  |  无数据
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/collect/cancelCollectCoupon/parameters/%7b%22passport%22%3a%22d2ab2b971ff0dc34b54c0eaa664873f0%22%2c%22coupon_id%22%3a%221%22%7d
     */
    public function cancelCollectCoupon()
    {
        $coupon_id = json_decode($this->_parameters['coupon_id'], true);
        if (!is_array($coupon_id)) {
            $this->_returnError(10044, '优惠券ID不合法');
        }
        foreach ($coupon_id as $value) {
            if (!preg_match('/^[1-9][0-9]*$/', $value)) {
                $this->_returnError(10044, '优惠券ID不合法');
            }
        }
        unset($num);

        foreach ($coupon_id as $value) {
            $num = M('coupons_favorite')->where(array('user_id' => $this->user_id, 'coupon_id' => $value))->delete();
            if (preg_match('/^[1-9][0-9]*$/', $num)) {
                M('coupons_sale')->where(array('coupon_id' => $value))->setDec('favorite_count');
            }
            unset($num);
        }

        $this->_returnData();
    }

    /**
     * 优惠券收藏列表 \n
     * URI : /home/collect/couponList
     * @param :
     *    name    | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters：
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *  passport | string | 必填 |  用户登录凭证
     *
     * @return
     *    name    |  type  | description
     * -----------|--------|--------------
     * coupon_list|  array |  收藏的优惠券列表
     *
     * coupon_list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   coupon_id  |  int   |  优惠券ID
     *  coupon_name | string |  优惠券名称
     *  coupon_desc | string |  描述
     * coupon_price | float  |  实际价格
     * market_price | float  |  原价
     * coupon_sales |  int   |  销量
     * coupon_img   | string |  引导图
     *    grade     | float  |  评分
     *
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/collect/couponList/parameters/%7b%22passport%22%3a%22d2ab2b971ff0dc34b54c0eaa664873f0%22%2c%22store_id%22%3a%221%22%7d
     */
    public function couponList()
    {
        $list = M('coupons_favorite')->where(array('user_id' => $this->user_id))->field('collect_id,coupon_id')->select();
        if ($list) {
            $i = 0;
            foreach ($list as &$value) {
                $coupon = M('coupons_sale')->where(array('coupon_id' => $value['coupon_id']))->field('coupon_id,coupon_name,coupon_price,market_price,coupon_sales,coupon_img')->find();
                if ($coupon) {
                    $coupon_list[$i] = $coupon;
                    $coupon_list[$i]['grade'] = 4.5;
                    $i++;
                } else {
                    M('coupons_favorite')->where(array('collect_id' => $value['collect_id']))->delete();
                    M('coupons_sale')->where(array('coupon_id' => $value['coupon_id']))->setDec('favorite_count');
                }
                unset($coupon);
            }
            unset($value);
        }

        if (!isset($coupon_list)) {
            $coupon_list = array();
        }
        $this->_returnData(['coupon_list' => $coupon_list]);
    }

    /**
     * 商家收藏列表 \n
     * URI : /home/collect/storeList
     * @param :
     *    name    | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters：
     *    name   |  type  | null | description
     * ----------|--------|------|-------------
     *  passport | string | 必填 |  用户登录凭证
     *
     * @return
     *    name    |  type  | description
     * -----------|--------|--------------
     * store_list|  array |  收藏的优惠券列表
     *
     * coupon_list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   coupon_id  |  int   |  优惠券ID
     *  coupon_name | string |  优惠券名称
     *  coupon_desc | string |  描述
     * coupon_price | float  |  实际价格
     * market_price | float  |  原价
     * coupon_sales |  int   |  销量
     * coupon_img   | string |  引导图
     *    grade     | float  |  评分
     *
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/collect/couponList/parameters/%7b%22passport%22%3a%22d2ab2b971ff0dc34b54c0eaa664873f0%22%2c%22store_id%22%3a%221%22%7d
     */
    public function storeList()
    {
        $list = M('store_favorite')->where(array('user_id' => $this->user_id))->field('collect_id,store_id')->select();
        if ($list) {
            $i = 0;
            foreach ($list as &$value) {
                $store = M('store')->where(array('store_id' => $value['store_id']))->field('store_id,store_name,comment_count,label,keywords,avg_price,store_banner,store_phone,address')->find();
                if ($store) {
                    $store_list[$i] = $store;
                    $store_list[$i]['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                    $store_list[$i]['distance'] = '<500m';//距离
                    $i++;
                } else {
                    M('store_favorite')->where(array('collect_id' => $value['collect_id']))->delete();
                    M('store')->where(array('store_id' => $value['store_id']))->setDec('favorite_count');
                }
                unset($store);
            }
            unset($value);
        }

        if (!isset($store_list)) {
            $store_list = array();
        }
        $this->_returnData(['store_list' => $store_list]);
    }

}