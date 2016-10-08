<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;
require_once('lib/wxpay/WxPayPubHelper.class.php');

/**
 * 微信支付接口
 * @author wubuze
 * @package Home\Controller
 */
class PayController extends ApiController
{ // 接口必须继承这个控制器

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();


    }


    /**
     * ##去付款（微信）
     * URI : /Home/orders/toPay
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string  | 必填    |用户票据
     * order_id | int     |  必填  | 订单id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * jsApiParameters |string  | 微信支付必要的参数（微信支付是必须转成json格式）\n 详见[微信接口文档](https://pay.weixin.qq.com/wiki/doc/api/index.html)
     *
     */
    public function toPay(){
        $order_id = I("order_id");
        $order = M('Orders')->where(['id'=>$order_id])->field('id,order_status,order_sn,goods_amount,goods,credit_num')->find();
        if(!$order){
            $this->_returnError('60010','订单号有误');
        }elseif($order['order_status']>0){
            $this->_returnError('60011','订单已经付款');
        }

        //计算订单应付款项
        $money = $order['goods_amount'];
        //优惠券计算

        //积分计算
        if($order['credit_num']){
            $credit = M('Credit')->where(['user_id'=>$order_id])->getField('total_credit');
            if($credit<$order['credit_num']){
                //积分不足
                M('Orders')->where(['id'=>$order_id])->setField('credit_num',0);
            }else{
                $money = $money-$order['credit_num']*0.01;
            }

        }
        $sn = $order['order_sn'];

        $user = $this->_checkPassport();
        $openid  = $user['openid'];


        if(!$openid){
            $this->_returnError('1','用户openid有误');
        }

        //=========步骤1：网页授权获取用户openid============
        //通过code获得openid

        //使用jsapi接口
        $jsApi = new \JsApi_pub();
        //$openid = 'oG7KGwJcCoAWHzZJTACiY4LL95M8';

        //=========步骤2：使用统一支付接口，获取prepay_id============
        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();
        $unifiedOrder->setParameter("openid",$openid);//
        $unifiedOrder->setParameter("body","gwshop商品");//商品描述
        //自定义订单号，此处仅作举例

        $unifiedOrder->setParameter("out_trade_no",$sn);//商户订单号
       	$unifiedOrder->setParameter("total_fee",$money*100);//总金额
        //$unifiedOrder->setParameter("total_fee",1);//总金额


        $notifyUrl = SITE_URL."/home/pay/notifyUrl";
        $unifiedOrder->setParameter("notify_url", $notifyUrl);//通知地址

        $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
        //非必填参数，商户可根据实际情况选填

        $prepay_id = $unifiedOrder->getPrepayId();
        //print_r($prepay_id);exit;
        //=========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);

        $jsApiParameters = $jsApi->getParameters();

        $data = ['jsApiParameters'=>$jsApiParameters];
        $this->_returnData($data);




    }





    public function notifyUrl()
    {

        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //$this->logger($postStr);

        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

        $out_trade_no = strval($postObj->out_trade_no);
        $money = strval($postObj->total_fee);
        //$attach       = strval($postObj->attach);

        $this->logger($out_trade_no);
        $order = M("Orders")->field('id,order_status,order_sn,credit_num,user_id')->where(array('order_sn'=>$out_trade_no))->find();
        if($order['order_status'] >= 1){
            $this->logger('ok2');
        }else{
            if($postObj->result_code=='SUCCESS'){
                //支付成功改变订单状态
                M("Orders")->where(array('order_sn'=>$out_trade_no))->setField("order_status",1);
                //写入实际付款金额
                M("Orders")->where(array('order_sn'=>$out_trade_no))->setField("order_amount",$money);
                //积分扣除
                //M('Credit')->where(['user_id'=>$order['user_id']])->setDec('total_credit',$order['credit_num']);

                //优惠券扣除
            }
        }
        echo "<xml>
            <return_code><![CDATA[SUCCESS]]></return_code>
            <return_msg><![CDATA[OK]]></return_msg>
            </xml>";
        exit;

    }

    function logger($log_content)
    {
        $max_size = 100000;
        $log_filename = "log.xml";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);

    }










}//end