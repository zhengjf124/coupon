<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;


/**
 * 订单接口
 * @author wubuze
 * @package Home\Controller
 */
class OrderController extends ApiController
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();

    }


    /**
     * ##订单列表
     * URI : /Home/order/index
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string | 必填 |用户票据
     * offset  |   int    |  选填  |分页开始
     * limit   |   int    |  选填  |每页几条
     * order_status|int   |  选填  |订单状态。100-待付款；200-待发货；101-已取消；210-待收货；220-待评价；222-交易成功； 300-退款中；310-退款成功；
     *
     * @return
     * name   | type     | description
     * --------|----------|----------------------
     * list     | array   | 订单列表二维数组
     * count    | int     | 订单总数
     *
     * list:
     * name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 订单id
     * order_amount | decimal  | 实际付款金额
     * order_sn | string  | 订单编号
     * add_time   | int     | 下单时间
     * order_status| int    | 订单状态： 同上
     * goods    | array    | 商品列表
     *
     * goods :
     * name | type | description
     * -----|------|-----------
     * id   |  int |  商品id
     * goods_name |  string |  商品名称
     * goods_price |  decimal |  商品价格
     * goods_img   |  string |  商品图片
     * goods_num   |  int |  商品数量
     * spec_key_name   |  string |  商品规格
     *
     */

    public function index()
    {
        $user = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        //自动取消订单
        $orderLogic = new \Home\Logic\OrderLogic();
        //$orderLogic -> autoCancelOrder($userId);

        $order_status = I('order_status',0);
        $where = [];
        if($order_status){
            $where = ['order_status' => $order_status];
        }
        $where = ['user_id' => $userId,'is_delete' => 0];
        $offset = I('offset') ? I('offset') : 0;
        $limit = I('limit') ? I('limit') : 20;
        $count = M('Order')->where($where)->count();
        $list = M('Order')->where($where)
            ->field('id,order_sn,order_amount,add_time,order_status,goods')->limit($offset, $limit)->order('add_time desc')->select();
        foreach ($list as & $val) {
            $goods = json_decode($val['goods'], true);
            $val['goods'] = $goods ? $goods : [];

        }
        $data = ['list' => $list, 'count' => $count];

        $this->_returnData($data);


    }


    /**
     * ##删除订单
     * URI : /Home/order/delete
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     *  id      |   int    |  必填  |订单id
     *
     * @return
     * name   | type     | description
     * -------|----------|----------------------
     * id     | int      | 订单id
     *
     *
     */


    public function delete()
    {
        $user = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        $id = I('id');
        $status = M('Order')->where(['user_id' => $userId, 'id' => $id])->getField('order_status');
        if ($status != 101) {//取消的订单才能删除
            $this->_returnError('60010', '订单状态有误');
        }else{
            $order = M('Order')->where(['user_id' => $userId, 'id' => $id])->setField('is_delete', 1);

        }

        $this->_returnData(['id' => $id]);


    }

    /**
     * ##取消订单
     * URI : /Home/order/cancel
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     *  id      |   int    |  必填  |订单id
     *
     * @return
     * name   | type     | description
     * -------|----------|----------------------
     * id     | int      | 订单id
     *
     *
     */
    public function cancel()
    {
        $user = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        $id = I('id');
        $orderLogic = new \Home\Logic\OrderLogic();
        $orderLogic -> cancelOrder($id,$userId);

    }

    /**
     * ##确认收货
     * URI : /Home/order/delivery
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     *  id      |   int    |  必填  |订单id
     *
     * @return
     * name   | type     | description
     * -------|----------|----------------------
     * id     | int      | 订单id
     *
     *
     */
    public function delivery()
    {
        $user = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        $id = I('id');
        $orderLogic = new \Home\Logic\OrderLogic();
        $orderLogic -> delivery($id,$userId);

    }

    /**
     * ##物流跟踪
     * URI : /Home/order/logistics
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     *  id      |   int    |  必填  |订单id
     *
     * @return
     * name   | type     | description
     * -------|----------|----------------------
     * id     | int      | 订单id
     * order_sn|string    | 订单编号
     * logistics_tracking |array |数组
     *
     * logistics_tracking:
     * name   | type     | description
     * -------|----------|----------------------
     * ftime  |string    | 时间
     * context|string    | 跟踪信息
     *
     *
     */
    public function logistics()
    {
        $user = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        $id = I('id');
        $order = M('Order')->where(['user_id' => $userId, 'id' => $id])->field('id,order_sn,logistics_tracking')->find();
        if (!$order) {
            $this->_returnError('60003', '订单不存在');
        }
        //物流跟踪信息(快递100)
        $logistics_tracking = json_decode(stripslashes($order['logistics_tracking']), true);
        if($logistics_tracking==false){
            $order['logistics_tracking'] = [];
        }else{
            $order['logistics_tracking'] = $logistics_tracking['lastResult']['data'];

        }
        $this->_returnData($order);

    }

    /**
     * ##订单详情接口
     * URI : /Home/order/detail
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     * id       | int   | 必填 | 订单id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 订单id
     * order_amount | decimal  | 实际付款金额
     * goods_amount | decimal  | 商品总金额
     * add_time   | int     | 下单时间
     * order_status| int    | 订单状态：100-待付款；200-待发货；101-已取消；210-待收货；220-待评价；222-完成
     * order_sn   | string    | 订单编号
     * inv_payee   | string    | 发票抬头
     * credit_money | decimal  | 积分抵扣
     * shipping_fee | decimal  | 配送费
     * coupon_price | decimal  | 优惠券抵扣
     * postscript   | string    | 订单附言
     * goods    | array    | 商品列表  二维数组
     * user_address  | array  | 用户收货地址 一维
     * logistics_tracking  | array  | 物流跟踪信息数组
     * goods_like    | array  | 你可能喜欢的商品二维数组
     *
     * goods :
     * name | type | description
     * -----|------|-----------
     * id   |  int |  商品id
     * goods_name |  string |  商品名称
     * goods_price |  decimal |  商品价格
     * goods_img   |  string |  商品图片
     * goods_num   |  int |  商品数量
     * spec_key_name   | string |  商品规格
     *
     * user_address :
     * name | type | description
     * -----|------|-----------
     * id    |  int |  收货地址id
     * consignee |  string |  收货人
     * mobile |  string |  收货人手机号
     * province   |  string |  省
     * city   |  string |  市
     * district   |  string |  地址
     * address   |  string |  详细地址
     *
     * logistics_tracking
     * name   | type     | description
     * -------|----------|----------------------
     * ftime  |string    | 时间
     * context|string    | 跟踪信息
     *
     * goods_like
     * name   | type     | description
     * -------|----------|----------------------
     * id  | int | 商品id
     * goods_name |string|商品名称
     * goods_price|decimal|商品价格
     * original_img|string|商品图片
     *
     */
    public function detail()
    {
        $user = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        $id = I('id');
        $order = M('Order')->where(['user_id' => $userId, 'id' => $id])
            ->field('id,order_amount,inv_payee,order_sn,add_time,credit_money,shipping_fee,goods_amount,coupon_price,postscript,order_status,goods,user_address,logistics_tracking')->find();
        $order['add_time'] = date("Y-m-d H:i:s",$order['add_time']);
        if (!$order) {
            $this->_returnError('60003', '订单不存在');
        }
        //物流跟踪信息(快递100)
        $logistics_tracking = json_decode($order['logistics_tracking'], true);
        if($logistics_tracking==false){
            $order['logistics_tracking'] = [];
        }else{
            $order['logistics_tracking'] = $logistics_tracking['lastResult']['data'];

        }
        $order['goods'] = json_decode($order['goods'], true);
        $order['user_address'] = json_decode($order['user_address'], true);

        $goods_like = M('Goods')->where('id>0')->field('id,goods_name,goods_price,original_img')->limit(4)->select();
        if($goods_like){
            $order['goods_like'] = $goods_like;
        }else{
            $order['goods_like'] = [];
        }


        $data = $order;
        $this->_returnData($data);


    }

    /**
     * ##用户确认订单接口
     * URI : /Home/order/confirm
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string | 必填 |用户票据
     * cart_id  | array   | 必填 |购物车id 数组 （cart_id:['1','4','5']）
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * user_credit | int  | 用户积分
     * credit_money | decimal  | 用户积分可抵扣金额
     * coupons    | array    | 可用优惠券列表数组（按价格倒叙排列）
     * goods    | array    | 订单商品列表数组
     * money    | decimal    | 订单商品总金额
     * address  | array    | 默认收货地址
     *
     *
     * goods :
     * name | type | description
     * -----|------|-----------
     * id   |  int |  购物车id
     * goods_id |  int |  商品id
     * goods_name |  string |  商品名称
     * market_price |  decimal |  市场价格
     * goods_price |  decimal |  商品价格
     * goods_img   |  string |  商品图片
     * goods_num   |  int |  商品数量
     * spec_key_name|  string |  商品规格
     *
     * coupons :
     * name | type | description
     * -----|------|-----------
     * id   |  int |  优惠券id
     * title |  string |  优惠券标题
     *   date    | string  |  使用期限
     *   money   | decimal |  优惠券金额
     * condition | decimal |  消费金额
     *
     * address :
     * name | type | description
     * -----|------|-----------
     * id       | int       |ID
     * consignee| string   | 收货人
     * province | string   | 省
     * city     | string   | 市
     * district | string   | 区
     * address  | string   | 详细地址
     * mobile   | string   | 手机号
     */
    public function confirm()
    {
        $user = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];

        $cartId = I('cart_id');
        $goods = [];
        $money = 0;
        if ($cartId) {
            if (!is_array($cartId)) {
                $this->_returnError('60001', '订单信息有误');
            }
            foreach ($cartId as $key => $val) {
                $goods[$key] = M('Cart')->where(['id' => $val])
                    ->field('id,goods_id,goods_name,goods_price,market_price,goods_num,spec_key_name')->find();
                $goods[$key]['goods_img'] = M('Goods')->where(['id' => $goods[$key]['goods_id']])->getField('goods_img');
                $money += $goods[$key]['goods_price'] * $goods[$key]['goods_num'];
            }
        } else {
            $this->_returnError('60001', '订单信息有误');
        }


        //用户积分
        $user_credit = M('Credit')->where(['user_id' => $userId])->getField('total_credit');
        $data['user_credit'] = $user_credit ? $user_credit : 0;
        //可抵扣
        $data['credit_money'] = $data['user_credit'] * 0.01;

        //优惠券列表
        $status = 100;//优惠券状态  100（可用），200（已用）
        $couponsLogic = new \Home\Logic\CouponLogic();
        $coupons = $couponsLogic->getCouponList($userId, $status, 0);
        foreach ($coupons as & $val) {
            $val['date'] = date('Y.m.d', $val['use_start_time']) . '-' . date('Y.m.d', $val['use_end_time']);
        }
        //获取默认收获地址
        $addressLogic = new \Home\Logic\AddressLogic();
        $address = $addressLogic->getDefault($userId);

        $data['coupons'] = $coupons;
        $data['money'] = $money;
        $data['goods'] = $goods;
        $data['address'] = $address;
        $this->_returnData($data);


    }

    /**
     * ##提交订单接口
     * URI : /Home/order/add
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport | string  | 必填 |用户票据
     * address_id| int    |   必填  | 收货地址id
     * cart_id   | array  |   必填  | 商品购物车id数组(从购物车结算必填此项) 如：（cart_id:['1','4','5']）
     * coupons_id| int    |   选填  | 优惠券id  0-不使用
     * shipping_id| int   |   选填  | 配送方式id   0-免运费
     * credit | int    |   选填  | 使用积分数量
     * postscript| string |   选填  |订单附言
     * inv_payee | int     |   必填  |发票类型  1-电子发票，2-个人发票 3-单位发票
     * inv_content| string|   选填  |发票抬头（选择单位发票时必填）
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * order_id| int      | 订单id
     *
     */
    public function add()
    {
        $user = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        $cartId = I("cart_id");

        $addressId = I("address_id",0);
        $couponsId = I("coupons_id") ? I("coupons_id") : 0;
        $shippingId = I("shipping_id") ? I("shipping_id") : 0;
        $credit = I("credit") ? I("credit") : 0;
        $postscript = I('postscript');
        $inv_payee = I('inv_payee',1);
        $inv_content = I('inv_content','');
//        if($inv_payee==3 && $inv_content=='')
//        {
//            $this->_returnError('60002', '发票信息有误');
//        }
        $address = M('UserAddress')->where(['id' => $addressId])->find();
        if (!$address) {
            $this->_returnError('60002', '收货地址不存在');
        }

        $goods = [];
        $money = 0;
        if ($cartId) {
            if (!is_array($cartId)) {
                $this->_returnError('60001', '订单信息有误' . $cartId);
            }
            foreach ($cartId as $key => $val) {
                $cartGoods = M('Cart')->where(['id' => $val])->field('goods_id,goods_name,goods_price,market_price,goods_num,spec_key_name')->find();
                if($cartGoods){
                    $goods[$key] = $cartGoods;
                }else{
                    $this->_returnError('60001', '订单信息有误' . $cartId);
                }
                $goods[$key]['goods_img'] = M('Goods')->where(['id' => $goods[$key]['goods_id']])->getField('goods_img');
                $money += $goods[$key]['goods_price'] * $goods[$key]['goods_num'];
            }
        } else {
            $this->_returnError('60001', '订单信息有误' . $cartId);
        }



        //积分计算
        if ($credit) {
            $total_credit = M('Credit')->where(['user_id' => $userId])->getField('total_credit');
            $total_credit = $total_credit ? $total_credit : 0;
            if ($credit > $total_credit) {
                $this->_returnError('60003', '您的积分数量不足');
            }
            $order['credit_num'] = $credit;//积分数量
            $point_rate = M('config')->where(['name'=>'point_rate'])->getField('value');
            $order['credit_money'] = $credit * (1/intval($point_rate));//积分抵扣金额
        }
        //优惠券
        if ($couponsId) {
            $couponsLogic = new \Home\Logic\CouponLogic();
            $coupons = $couponsLogic->checkCoupon($couponsId,$money);
            if ($coupons['code'] != 100) {
                $this->_returnError($coupons['code'], $coupons['message']);
            }
            $order['coupon_id'] = $couponsId;//优惠券id
            $order['coupon_price'] = $coupons['money'];
        }


        //配送费
        if ($shippingId) {
            //
            $order['shipping_fee'] = 0;//配送价格
        }

        $order['user_id'] = $userId;
        $order['postscript'] = $postscript;//订单附言
        $order['inv_payee'] = $inv_payee;//发票信息
        $order['inv_content'] = $inv_content;//发票信息
        $order['pay_status'] = 0;  //0-未付款; 1-付款中; 2-已付款
        $order['order_status'] = 100; //订单状态： 100-待付款；200-待发货；101-已取消；210-待收货；220-待评价；222-完成
        $order['add_time'] = time();
        $order['order_sn'] = time() . mt_rand(100, 999);
        $order['goods'] = json_encode($goods);//打包商品信息
        $order['user_address'] = json_encode($address);//打包收货地址信息
        $order['user_address_id'] = $addressId;
        $order['consignee'] = $address['consignee'];
        $order['mobile'] = $address['mobile'];
        $order['goods_amount'] = $money;//商品总金额
        $order['order_amount'] = $money + $order['shipping_fee'] - $order['coupon_price'] - $order['credit_money'];//应付款
        $orderId = M('Order')->data($order)->add();

        if($orderId){

            if($order['coupon_id']){
                //扣除优惠券(与优惠券的分类表相关)
                $couponsLogic -> useCoupon($userId, $couponsId, $orderId);
            }
            if($order['credit_num']){
                //扣除积分
                $credit_num = M('Order')->where(['id'=>$orderId])->getField('credit_num');
                M('Credit')->where(['user_id' => $userId])->setDec('total_credit',$credit_num);
            }


            //添加订单消息
            $orderLogic = new \Home\Logic\OrderLogic();
            $orderLogic->addOrderLog($userId, $orderId);
            //订单商品表
            // 1插入order_goods 表
            $cartWhere['user_id'] = $userId;
            $cartWhere['id'] = array('in', $cartId);
            $cartList = M('Cart')->where($cartWhere)->select();
            foreach ($cartList as $key => $val) {
                $data2['order_id'] = $orderId; // 订单id
                $data2['goods_id'] = $val['goods_id']; // 商品id
                $data2['goods_name'] = $val['goods_name']; // 商品名称
                $data2['goods_sn'] = $val['goods_sn']; // 商品货号
                $data2['goods_num'] = $val['goods_num']; // 购买数量
                $data2['market_price'] = $val['market_price']; // 市场价
                $data2['goods_price'] = $val['goods_price']; // 商品价
                $data2['spec_key'] = $val['spec_key']; // 商品规格
                $data2['spec_key_name'] = $val['spec_key_name']; // 商品规格名称
                // $data2['bar_code']           = $val['bar_code']; // 商品条码
                // $data2['member_goods_price'] = $val['member_goods_price']; // 会员折扣价
                // $data2['cost_price']         = $goods['cost_price']; // 成本价
                // $data2['give_integral']      = $goods['give_integral']; // 购买商品赠送积分
                // $data2['prom_type']          = $val['prom_type']; // 0 普通订单,1 限时抢购, 2 团购
                //$data2['prom_id']            = $val['prom_id']; // 活动id
                M("OrderGoods")->data($data2)->add();

            }
            //删除购物车
            //M('Cart')->where($cartWhere)->delete();

            $data = ['order_id' => $orderId];
            $this->_returnData($data);
        }else{
            $this->_returnError('10000','操作失败');
        }



    }

    /**
     * ##用户申请售后服务
     * URI : /Home/order/applyServer
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport | string  | 必填 |用户票据
     * order_id  | int    |   必填  | 订单id
     * goods_id   | int  |   必填  | 商品id
     * order_sn   | string|   必填  | 订单编号
     * reason | string    |   选填  | 申请原因
     * type         | int |   选填  |售后类型  0退货  1退款
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     *  id     | int      | 申请id  \n 如果 error_code=20002 表示用户的申请已经提交
     *
     */
    public function applyServer(){
        $user = $this->_checkPassport();//用户必须登入
        $orderSn = I('order_sn','');
        $orderId = I('order_id',0);
        $goodsId = I('goods_id',0);
        $reason = I('reason','');
        $type = I('type',0);
        if($orderId==0||$goodsId==0||$orderSn==''){
            $this->_returnError('20001','提交的参数不完整');
        }
        $add['user_id'] = $user['id'];
        $add['order_id'] = $orderId;
        $add['goods_id'] = $goodsId;
        $returnGoods = M('returnGoods')->field('id,type,reason,status,remark')->where($add)->find();
        if($returnGoods){
            $this->_returnError('20002','您已经提交过申请',$returnGoods);
        }
        $add['order_sn'] = $orderSn;
        $add['reason'] = $reason;
        $add['type'] = $type;
        $add['status'] = 0;
        $add['add_time'] = time();
        $id = M('returnGoods')->data($add)->add();
        if($id){
             M('Order')->where(['id'=>$orderId])->setField('order_status',300);//退款中
        }
        $this->_returnData(['id'=>$id]);
    }


}//end