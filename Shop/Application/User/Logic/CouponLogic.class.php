<?php
namespace Home\Logic;

use Think\Controller;

/**
 * GWSHOP优惠券
 * 包含优惠券的使用，查询等等
 * @author         xiaoxu<zhengjf124@qq.com>
 * @since          1.0
 */
class CouponLogic extends Controller
{

    /**
     * 使用优惠券
     * @access public
     * @param int $user_id 用户ID
     * @param int $coupons_id 优惠券ID
     * @param int $order_id 订单ID
     * @since 1.0
     * @return boolean
     */
    public function useCoupon($user_id, $coupons_id, $order_id)
    {
        $coupons_info = M('coupons')->where(['user_id' => $user_id, 'id' => $coupons_id])->find();
        if (!$coupons_info) {
            return false;//该优惠券不存在
        }
        if ($coupons_info['order_id']) {

        }
        $res = M('coupons')->where(['user_id' => $user_id, 'id' => $coupons_id])->save(['order_id' => $order_id, 'use_time' => time()]);
        if ($res) {
            //添加优惠券使用记录
            M('coupons_log')->data(['user_id' => $user_id, 'coupons_id' => $coupons_id, 'order_id' => $order_id])->add();
            //添加使用数量 +1
            M('coupons_type')->where(['id' => $coupons_info['coupons_type_id']])->setInc('use_num');
            return true;
        } else {
            return false;
        }
    }

    /**
     * 归还优惠券
     * @access public
     * @param int $user_id 用户ID
     * @param int $coupons_id 优惠券ID
     * @param int $order_id 订单ID
     * @since 1.0
     * @return boolean
     */
    public function backCoupon($user_id, $coupons_id)
    {
        $coupons_info = M('coupons')->where(['user_id' => $user_id, 'id' => $coupons_id])->find();

        $res = M('coupons')->where(['user_id' => $user_id, 'id' => $coupons_id])->save(['order_id' => 0, 'use_time' => 0]);
        if ($res) {
            //添加优惠券使用记录
            //M('coupons_log')->data(['user_id' => $user_id, 'coupons_id' => $coupons_id, 'order_id' => $order_id])->add();
            //添加使用数量 -1
            M('coupons_type')->where(['id' => $coupons_info['coupons_type_id']])->setDec('use_num');
            return true;
        } else {
            return false;
        }
    }


    /**
     * 注册发放优惠券
     * @access public
     * @param int $user_id 用户ID
     * @since 1.0
     * @return boolean
     */
    public function registerGrantCoupon($user_id)
    {
        $user_info = M('user')->where(['id' => $user_id])->field('id')->find();
        if (!$user_info) {
            return false;
        }
        $now = time();
        //获取优惠券对应的分类信息(注册发放的)
        $coupon_type_info = M('coupons_type')->where(['type' => 2, ['send_start_time' => ['elt', $now]], ['send_end_time' => ['egt', $now]]])->field('id,use_start_time,use_end_time')->select();
        if ($coupon_type_info) {
            foreach ($coupon_type_info as $value) {
                $data = ['coupons_type_id' => $value['id'], 'type' => 2, 'user_id' => $user_id, 'send_time' => $now,'title'=>'用户注册获得'];

                M('coupons')->data($data)->add();
                M('coupons_type')->where(['id' => $value['id']])->setInc('send_num', 1);
            }
            unset($value);
        }
        return true;
    }

    /**
     * 获取优惠券列表
     * @access public
     * @param int $user_id 用户ID
     * @param int $status 优惠券状态(100-可使用，200-已使用，300-已过期)
     * @param int $money 金额
     * @since 1.0
     * @return array
     *
     */
    public function getCouponList($user_id, $status, $money)
    {

        //$sql = "SELECT l.id,l.coupons_type_id,l.title,l.send_time,l.user_id,l.type,l.code,l.order_id,l.use_time,c.money,c.condition,c.use_start_time,c.use_end_time FROM __PREFIX__coupons l " .
         //   "LEFT JOIN __PREFIX__coupons_type c ON c.id = l.coupons_type_id " .
        //    "WHERE l.user_id = {$user_id} ORDER BY c.money DESC";
        $sql = "SELECT l.id,l.title,c.money,c.condition,c.use_start_time,c.use_end_time FROM __PREFIX__coupons l " .
            "LEFT JOIN __PREFIX__coupons_type c ON c.id = l.coupons_type_id " .
            "WHERE l.user_id = {$user_id} ORDER BY c.money DESC";
        $coupon_list = M()->query($sql);
        $data = [];
        if (in_array($status, [100, 200, 300])) {
            foreach ($coupon_list as $value) {
                $code_info = $this->checkCoupon($value['id']);
                $value['status'] = $code_info['code'];
                unset($code_info);
                if ($value['status'] == $status) {
                    if ($money > 0) {
                        if ($value['condition'] <= $money) {
                            $data[] = $value;
                        }
                    } else {
                        $data[] = $value;
                    }
                }
            }
            unset($value);
        } else {
            foreach ($coupon_list as $value) {
                $code_info = $this->checkCoupon($value['id']);
                $value['status'] = $code_info['code'];
                unset($code_info);
            }
            unset($value);
            $data = $coupon_list;
        }
        return $data;
    }

    /**
     * 获取一张优惠券详情
     * @access public
     * @param int $coupons_id 优惠券ID
     * @since 1.0
     * @return array
     * @note code: 100-可使用，200-已使用，300-已过期，400-该优惠券不存在，500-该优惠券分类不存在
     *
     */
    public function getCouponInfo($coupons_id)
    {

        //获取优惠券信息

        $sql = "SELECT l.id,c.title,l.coupons_type_id,l.send_time,l.user_id,l.type,l.code,l.order_id,l.use_time,c.money,c.condition,c.use_start_time,c.use_end_time FROM __PREFIX__coupons l " .
            "LEFT JOIN __PREFIX__coupons_type c ON c.id = l.coupons_type_id " .
            "WHERE l.id = {$coupons_id}";
        $coupon_info = M()->query($sql);
        //$coupon_info = M('coupons')->where(array('id' => $coupons_id))->field('id,coupons_type_id,send_time,user_id,type,use_time,order_id')->find();
        if (!$coupon_info) {
            $data = ['code' => 400, 'message' => '该优惠券不存在'];
            return $data;
        }
        return $coupon_info;
    }

    /**
     * 检查优惠券状态
     * @access public
     * @param int $coupons_id 优惠券ID
     * @since 1.0
     * @return array
     * @note code: 100-可使用，200-已使用，300-已过期，400-该优惠券不存在，500-该优惠券分类不存在
     *
     */
    public function checkCoupon($coupons_id,$money=0)
    {
        $where['id'] = $coupons_id;
        //获取优惠券信息
        $coupon_info = M('coupons')->where($where)->field('id,coupons_type_id,send_time,user_id,type,use_time,order_id')->find();
        if (!$coupon_info) {
            $data = ['code' => 400, 'message' => '该优惠券不存在'];
            return $data;
        }
        //判断金额
        $coupon_type_condition = 0;
        if($money > 0){
            $coupon_type_condition = M('coupons_type')->where(['id'=>$coupon_info['coupons_type_id']])->getField('condition');
            if($money<$coupon_type_condition){
                $data = ['code' => 400, 'message' => '该优惠券不满使用足条件'];
                return $data;
            }
        }

        if ($coupon_info['use_time'] > 0 && $coupon_info['order_id'] > 0) { //存在使用时间 和 订单ID 说明优惠券已使用
            $data = ['code' => 200, 'message' => '已使用'];
            return $data;
        }
        //获取优惠券对应的分类信息
        $coupon_type_info = M('coupons_type')->where(array('id' => $coupon_info['coupons_type_id']))->field('money,use_start_time,use_end_time')->find();
        if (!$coupon_type_info) {
            $data = ['code' => 500, 'message' => '该优惠券分类不存在'];
            return $data;
        }

        $time = [$coupon_type_info['use_start_time'], $coupon_type_info['use_end_time']];
        $now = time();
        if ($now > max($time) || $now < min($time)) { //在两个时间之外  //已过期
            $data = ['code' => 300, 'message' => '已过期'];
        } else {  //可以 使用
            $data = ['code' => 100, 'message' => '可使用','money'=>$coupon_type_info['money']];
        }
        return $data;
    }
}
