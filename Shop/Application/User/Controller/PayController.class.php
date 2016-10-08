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
     * ##去付款
     * URI : /Home/Pay/toPay
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
     *
     */
    public function toPay(){
        $user = $this->_checkPassport();
        $order_id = I("order_id");
        $order = M('Order')->where(['id'=>$order_id])->field('id,order_status,order_sn,goods_amount,goods,credit_num')->find();
        if(!$order){
            $this->_returnError('60010','订单号有误');
        }elseif($order['order_status']!=100){
            $this->_returnError('60011','订单信息有误，或已经处理。');
        }

        //计算订单应付款项
        $money = $order['goods_amount'];
        //优惠券计算

        //积分计算
        if($order['credit_num']){
            $credit = M('Credit')->where(['user_id'=>$order_id])->getField('total_credit');
            if($credit<$order['credit_num']){
                //积分不足
                M('Order')->where(['id'=>$order_id])->setField('credit_num',0);
            }else{
                $money = $money-$order['credit_num']*0.01;
            }

        }

        //$order['order_amount'] = 0;


        $sn = $order['order_sn'];
        $openid  = $user['openid'];
        if(!$openid){
            $this->_returnError('60011','用户openid有误');
        }
        //$sn = 'asdfasdfas';
        //$site = 'http://gwshop.usrboot.com';
        $site = C('site_url');
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
        //	$unifiedOrder->setParameter("total_fee",$money*100);//总金额
        $unifiedOrder->setParameter("total_fee",1);//总金额

        //$notifyUrl=$site."/wxpay/notice.php";
        $notifyUrl = $site."/home/pay/notifyUrl";
        $unifiedOrder->setParameter("notify_url", $notifyUrl);//通知地址

        $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
        //非必填参数，商户可根据实际情况选填

        $prepay_id = $unifiedOrder->getPrepayId();
        //print_r($prepay_id);exit;
        //=========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);

        $jsApiParameters = $jsApi->getParameters();
        //$this->assign('jsApiParameters',$jsApiParameters);
        //print_r($jsApiParameters);
        $data['jsApiParameters'] = $jsApiParameters;
        $this->_returnData($data);
        //$this->display();

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
        $order = M("Order")->field('id,pay_status,order_status,order_sn,credit_num,user_id')->where(array('order_sn'=>$out_trade_no))->find();
        if($order['pay_status'] == 200){//订单状态：100-待付款；200-待发货；101-已取消；210-待收货；220-待评价；222-完成
            $this->logger('ok2');
        }else{
            if($postObj->result_code=='SUCCESS'){
                //支付成功改变订单状态
                $order_save['order_status'] = 200;
                $order_save['pay_status'] = 1;
                $order_save['money_paid'] = $money; //写入实际付款金额
                $order_save['pay_time'] = time(); //写入实际付款金额
                M("Order")->where(array('order_sn'=>$out_trade_no))->save($order_save);

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