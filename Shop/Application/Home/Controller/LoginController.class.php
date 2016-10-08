<?php

namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
require_once('lib/alidayu/TopSdk.php');
use Application\ApiController;

/**
 * 用户登入注册接口
 * @author xiaoxu
 * @package Home\Controller
 *
 */
class LoginController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->_url = C("site_url") . '/index.php?m=home&c=wxLogin&a=ind&redirect_uri=';

    }


    /**
     * 用户注册接口 \n
     * URI : /home/login/register
     * @param :
     *     name   | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters：
     *    name    |   type   |  null  | description
     * -----------|----------|--------|-------------
     *   mobile   |  string  |  必填  |    手机号码
     *  password  |  string  |  必填  |    密码
     *  user_from |  string  |  必填  | 用户来源(android、ios、web、wx)
     *  note_code |   int    |  必填  |    短信验证码
     *    time    |  string  |  必填  |     时间
     *
     * @return
     *   name   |  type  | description
     * ---------|--------|--------------
     * passport | string | 用户票据
     *
     */
    public function register()
    {
        $parameters = $this->_createParameters();
        if (!preg_match('/^1[34578][0-9]{9}$/', $parameters['mobile'])) {
            $this->_returnError('10008', '手机号码不合法');
        }

        /*        $nick_name = trim($parameters['nick_name']);
                if (empty($nick_name) === true) {
                    $this->_returnError('10005', '用户昵称为空');
                }*/

        if (!preg_match('/^[\w+]{6,16}$/', $parameters['password'])) {
            $this->_returnError('10004', '密码不合法');
        }

        $note_code = $parameters['note_code'];
        if (!preg_match('/^[0-9]{4,6}$/', $note_code)) {
            $this->_returnError('10007', '短信验证码不合法');
        }

        /*用户来源*/
        $all_from = ['android', 'ios', 'web', 'wx'];
        if (!in_array($parameters['user_from'], $all_from)) {
            $this->_returnError('10006', '用户来源不合法');
        }

        $code_info = $this->getNoteCode($parameters['mobile']);
        if ($note_code != $code_info['code']) {
            $this->_returnError(10011, '短信验证码错误');
        }

        if ($parameters['mobile'] != $code_info['mobile']) {
            $this->_returnError(10012, '接收短信的手机号与提交的手机号不匹配');
        }

        $user = D('User')->checkField('mobile', $parameters['mobile']);
        if ($user) {
            $this->_returnError('10003', '手机号码已经被注册');
        }

        $data['mobile'] = $parameters['mobile'];
        $data['password'] = $this->passwordEncryption($parameters['password']);
        $data['nick_name'] = $parameters['mobile'];
        $data['user_from'] = $parameters['user_from'];
        $data['reg_time'] = $this->_now;
        $data['last_login'] = $this->_now;
        $data['login_count'] = 1;
        $data['last_ip'] = get_client_ip();
        $user_id = M('user')->data($data)->add();
        $passport = $this->_createPassport($user_id);
        $data = [
            'passport' => $passport
        ];
        $this->_returnData($data);
    }


    /**
     * 用户登入接口 \n
     * URI : /home/login/login
     * @param :
     *     name   | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters:
     *     name   |  type  | null | description
     * -----------|--------|------|-------------
     *   mobile   | string | 必填  |  手机号码
     *  password  | string | 必填  |   密码
     *    time    | string | 必填  |   时间
     *
     * @return
     *  name   |  type  | description
     * --------|--------|-------------
     * passport| string |  用户票据
     *
     */
    public function login()
    {
        $parameters = $this->_createParameters();
        if (!preg_match('/^1[34578][0-9]{9}$/', $parameters['mobile'])) {
            $this->_returnError('10008', '手机号码不合法');
        }

        if (!preg_match('/^[\w+]{6,16}$/', $parameters['password'])) {
            $this->_returnError('10004', '密码不合法');
        }

        $user = D('User')->checkField('mobile', $parameters['mobile']);
        if (!$user) {
            $this->_returnError('10009', '用户名或密码错误');
        }

        if ($this->passwordEncryption($parameters['password']) != $user['password']) {
            $this->_returnError('10009', '用户名或密码错误');
        }

        $passport = $this->_createPassport($user['user_id']);
        $data = [
            'passport' => $passport
        ];
        $this->_returnData($data);
    }


    /**
     * 发送短信验证码 \n
     * URI : /home/login/sendNoteCode
     * @param :
     *     name   | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters:
     *     name   |  type  | null | description
     * -----------|--------|------|-------------
     *   mobile   | string | 必填  |  手机号码
     *
     * @return
     *  name   |  type  | description
     * --------|--------|-------------
     * ------- | -----  |   无
     *
     */
    public function sendNoteCode()
    {
        $parameters = $this->_createParameters();
        if (!preg_match('/^1[34578][0-9]{9}$/', $parameters['mobile'])) {
            $this->_returnError('10008', '手机号码不合法');
        }
        if ($this->getNoteCode($parameters['mobile'])) {
            $this->_returnError(10013, '短信已发送，请勿重复操作');
        } else {
            $code = rand(100000, 999999);
            date_default_timezone_set('Asia/Shanghai');
            $c = new \TopClient;
            $appkey = '23471823';
            $secret = '33bd1b34ce9ca370adf3d6493e8c4759';
            $c->appkey = $appkey;
            $c->secretKey = $secret;
            $req = new \AlibabaAliqinFcSmsNumSendRequest;
            //$req->setExtend("123456");
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("大鱼测试");
            $req->setSmsParam('{"code":"' . $code . '","product":"E购联盟"}');
            $req->setRecNum($parameters['mobile']);
            $req->setSmsTemplateCode("SMS_16751324");
            $resp = $c->execute($req);
            $resp = $this->object_array($resp);
            if ($resp['err_code'] == 0) {
                $this->saveNoteCode(array('code' => $code, 'mobile' => $parameters['mobile']));
                $this->_returnData();
            } else {
                $this->_returnError(10014, '短信发送失败，请重试');
            }
        }
    }


    /*    public function test()
        {
                    date_default_timezone_set('Asia/Shanghai');
                    $c = new \TopClient;
                    $appkey = '23471823';
                    $secret = '33bd1b34ce9ca370adf3d6493e8c4759';
                    $c->appkey = $appkey;
                    $c->secretKey = $secret;
                    $req = new \AlibabaAliqinFcSmsNumSendRequest;
                    //$req->setExtend("123456");
                    $messinfo2['messphone'] = '13675091372';
                    $vcode = 1234;
                    $req->setSmsType("normal");
                    $req->setSmsFreeSignName("大鱼测试");
                    $req->setSmsParam('{"code":"' . $vcode . '","product":"E购联盟"}');
                    $req->setRecNum($messinfo2['messphone']);
                    $req->setSmsTemplateCode("SMS_16751324");
                    $resp = $c->execute($req);
                    $resp = $this->object_array($resp);
                    if ($resp['err_code'] == 0) {

                    }
                    echo json_encode($resp);
            exit;
            //$parameters = '%7b%22mobile%22%3a%2213688888888%22%2c%22password%22%3a%22123456%22%2c%22time%22%3a%221474941959%22%7d';
                   $a['mobile'] = 13688888888;
                    $a['password'] = 123456;
                    $a['time'] = 1474941959;
                    $b = json_encode($a);
                    //$parameters = $b;
                    $parameters = urlencode($b);
                    $data['parameters'] = $parameters;
                    echo $this->_curlPost('http://192.168.1.100/home/login/login', $data);
                    //echo $this->_curlGet('http://192.168.1.100/home/login/login?parameters=' . $parameters);
        }*/

    /**
     * 将对象转换为数组
     * @param $array
     * @return array
     */
    private function object_array($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

}//end
