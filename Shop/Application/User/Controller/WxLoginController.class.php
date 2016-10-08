<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 微信用户授权
 * @author wubuze
 * @package Home\Controller
 */
class WxLoginController extends ApiController
{ // 接口必须继承这个控制器


    /**
     *构造函数
     *
     */
    public function __construct() {
        parent::__construct();
        $this->wxSet = M("WxUser")->where('1=1')->find();

    }

    /**
     * 微信浏览器用户授权获取票据接口 \n
     * URI : /Home/WxLogin/ind
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * redirect_uri | string | 必须 | 最后跳转url ,请使用encodeURI()
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * _passport| string   | 用户票据
     *
     */
    public  function ind(){
        $state = I('redirect_uri');
        if(!$state){
            $this->_returnError('1','错误');
        }

        $app =  $this->wxSet;
        $site = C('site_url');
        $uri = $site."/home/wxLogin/wx_oauth";
        //$uri='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];//当前页面完整url
        $uri=urlencode($uri);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$app['appid']."&redirect_uri=".$uri."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
        header("Location: ".$url); exit;
    }

    /**
     * 微信授权
     */
    public function wx_oauth(){
        $code= I('code');
        $back_url = I('state');
        $back_url = urldecode($back_url);
        //网页授权获取用户信息
        $app = $this->wxSet;
        $appid =  $app['appid'];
        $secret=  $app['appsecret'];

        if(!$code){
           echo 'no';
        }else{//网页授权第二步 使用code换取openid

            $url2="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
            $info=$this->_curlGet($url2);
            $info=json_decode($info,true);



            if(!$info['openid']){//授权失败

                header("Location: ".$back_url); exit();
//                echo 'test';exit;
//                $info=$this->_curlGet($url2);
//                $info=json_decode($info,true);

            }


            //保存
            $dbUser = M("User");
            $user=$dbUser->field('id,openid')->where(array('openid'=>$info['openid']))->find();
            if(!$user){//添加新用户
                $url3 = "https://api.weixin.qq.com/sns/userinfo?access_token=".$info['access_token']."&openid=".$info['openid']."&lang=zh_CN";
                $wxinfo=$this->_curlGet($url3);
                $wxinfo=json_decode($wxinfo,true);

                if($wxinfo){
                    $data_s['nick_name'] = $wxinfo['nickname'];
                    $data_s['headimgurl'] = $wxinfo['headimgurl'];
                }

                //print_r($wxinfo);exit;

                $data_s['openid']=$info['openid'];
                $data_s['wx_uid'] = 1;  //微信用户
                $data_s['user_name'] = "wx".time().mt_rand(100,999);
                $data_s['reg_time']=time();
                $dbUser->create($data_s);
                $newid = $dbUser->data($data_s)->add();
                //注册发放优惠券
                $couponsLogic = new \Home\Logic\CouponLogic();
                $couponsLogic->registerGrantCoupon($newid);
                $user=$dbUser->field('id,openid')->where(array('openid'=>$info['openid']))->find();
            }

            $passport = $this->_createPassport($user);


            if (preg_match("/\&/", $back_url)){
                if(preg_match("/\&_passport=([a-zA-Z0-9]*)$/", $back_url)){
                    $back_url = preg_replace('/\&_passport=([a-zA-Z0-9]*)$/', '', $back_url);
                    $url = $back_url.'&_passport='.$passport;
                }elseif(preg_match("/\?_passport=([a-zA-Z0-9]*)$/", $back_url)){
                    $back_url = preg_replace('/\?_passport=([a-zA-Z0-9]*)$/', '', $back_url);
                    $url = $back_url.'?_passport='.$passport;
                }else{
                    $url = $back_url.'&_passport='.$passport;
                }
            }elseif(preg_match("/\?/", $back_url)){
                if(preg_match("/\?_passport=([a-zA-Z0-9]*)$/", $back_url)){
                    $back_url = preg_replace('/\?_passport=([a-zA-Z0-9]*)$/', '', $back_url);
                    $url = $back_url.'?_passport='.$passport;
                }else{
                    $url = $back_url.'&_passport='.$passport;
                }
            }else{
                $url = $back_url.'?_passport='.$passport;
            }


            header("Location: ".$url); exit();




        }



    }








}//end