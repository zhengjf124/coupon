<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 优惠券接口
 * @author wubuze
 * @package Home\Controller
 */
class CouponsController extends ApiController
{

    /**
     *构造函数
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->couponLogic = new \Home\Logic\CouponLogic();
    }

    /**
     * 优惠券列表接口 \n
     * URI : /Home/Coupons/index/
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string   | 必填 |用户票据
     * status   | int      | 必填 | 100(可用)， 200（已用），300（过期）
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     *  list    |  array  |  优惠券列表二维数组
     *  count   |  int    |  数组长度
     *
     * list :
     *   name    | type    | description
     * ----------|---------|----------------------
     *    id     |  int    |  优惠券id
     *   title   | string  |  标题
     *   date    | string  |  使用期限
     *   money   | decimal |  优惠券金额
     * condition | decimal |  消费金额
     *
     */
    public function index()
    {

        $user  = $this->_checkPassport();//用户必须登入
        $user_id = $user['id'];

        $status = I('status')?I('status'):100;//status 优惠状态 100（可用） 200(传的status 不等于 100 200 300 的话 默认返回所有的优惠券)
//
        $money = I('money') ? I('money') : 0; //订单商品总额
        $couponsLogic = new \Home\Logic\CouponLogic();
        $coupons = $couponsLogic->getCouponList($user_id, $status, $money);
        foreach ($coupons as & $val) {
            $val['date'] = date('Y.m.d', $val['use_start_time']) . '-' . date('Y.m.d', $val['use_end_time']);
        }
        //$count = 1;
        $data = ['list' => $coupons, 'count' => count($coupons)];
        $this->_returnData($data);
    }

//    function test(){
//
//        $newid = 1;
//        //注册发放优惠券
//        $couponsLogic = new \Home\Logic\CouponLogic();
//        $couponsLogic->registerGrantCoupon($newid);
//    }




}//end