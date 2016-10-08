<?php
/**
 * Created by PhpStorm.
 * User: wbz
 * Date: 16/7/22
 * Time: 下午2:56
 */
namespace Home\Logic;
use Think\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

class OrderLogic extends ApiController
{
    /**
     * 添加用户订单消息
     */
    public function addOrderLog($userId,$orderId){
        $order = M('Order')->field('id,order_sn,order_amount')->find();
        $data['user_id'] = $userId;
        $data['order_id'] = $orderId;
        $data['add_time'] = time();
        $data['title'] = '用户订单消息';
        $data['content'] = "您好，您的订单已经生成，您的订单号是：".$order['order_sn'].",应付款：".$order['order_amount']."，请尽快支付";
        $id = M('noticeOrder')->data($data)->add();
        if($id){
            //添加未读消息数量
            $type = 'order';
            $msg = M('readMsg')->where(['user_id'=>$userId, 'type'=>$type])->find();
            if($msg)
            {
                M('readMsg')->where(['user_id'=>$userId])->setInc('num');

            }else{
                M('readMsg')->data(['user_id'=>$userId, 'num'=>1, 'type'=>$type])->add();

            }
        }
        return $id;
    }

    /**
     * 取消订单
     * @param $id
     * @param $userId
     */
    public function cancelOrder($id,$userId){
        $order = M('Order')->where(['user_id' => $userId, 'id' => $id,'order_status'=>100])->setField('order_status', 101);
        if(!$order){
            $this->_returnError('60011', '订单状态有误');
        }
        //取消日志
        //返还优惠券等
        $this->backCouponAndCredit($id,$userId);
        $this->_returnData($order['id']);
    }

    /**
     * 退还优惠券和积分
     */
    public function backCouponAndCredit($orderId,$userId){
        $order = M('Order')->field('id,credit_num,coupon_id')->where(['id' => $orderId])->find();
        if($order['credit_num']>0){
            M('Credit')->where(['user_id' => $userId])->setInc('total_credit',$order['credit_num']);
            //归还积分日志
        }
        if($order['coupon_id']>0){
            $couponLogic = new \Home\Logic\CouponLogic();
            $couponLogic -> backCoupon($userId,$order['coupon_id']);

        }

    }


    /**
     * 确认收货
     * @param $id
     * @param $userId
     */
    public function delivery($id,$userId){
        $order_status = M('Order')->where(['user_id' => $userId, 'id' => $id])->getField('order_status');
        if($order_status!=210){//待收获
            $this->_returnError('60011', '订单状态有误');
        }
        M('Order')->where(['user_id' => $userId, 'id' => $id])->setField('order_status', 220);
        $this->_returnData($id);
    }

    /*
     * 自动取消订单(批量)
     */
    public function autoCancelOrder($userId)
    {
        $orderCancelTime = M('active')->where(['type'=>'order_cancel'])->getField('end_time');
        if($orderCancelTime){
            $nowTime = time()+$orderCancelTime;
            $wh['add_time'] = array('elt',$nowTime);
            //取消未付款的订单列表
            $orderList = M('Order')->field('id')->where(['user_id'=>$userId,'order_status'=>100])->select();
            foreach($orderList as $val){
                //M('Order')->where(['id'=>$val['id'],'order_status'=>100])->setField('order_status',101);
                $this->cancelOrder($val['id'],$userId);
            }


        }
    }












}
