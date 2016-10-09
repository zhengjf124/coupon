<?php
namespace Application;

use Think\Controller;

class ApiController extends Controller
{
    //protected $_url = '';

    protected $_now = '';

    protected $_parameters = [];


    public function __construct()
    {
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        $this->_parameters = $this->_createParameters();
        //$this->_checkSign();
        $this->_now = time();
    }


    /**
     * 验证passport 返回登录的用户信息
     * @return bool| array
     */
    protected function _checkPassport()
    {
        if (!preg_match('/^[0-9a-zA-Z]{32}$/', $this->_parameters['passport'])) {
            $this->_returnError('10010', 'passport不合法');
        }
        $user_id = M('user_passport')->getFieldByPassport($this->_parameters['passport'], 'user_id');
        if ($user_id) {
            $userInfo = M('user')->field('user_id,nick_name,mobile,headimgurl,sex')->find($user_id);
            if ($userInfo) {
                return $userInfo;
            }
        }
        $this->_returnError('10010', 'passport不合法');
    }


    /**
     * 获取、保存登录票据
     * @param int $user_id 用户ID
     * @param string $type 用户类型（来源 - app web wx）
     * @return mixed|string
     */
    protected function _createPassport($user_id, $type = 'app')
    {
        $passport = M('user_passport')->getFieldByUser_id($user_id, 'passport');//where(['user_id' => $user_id])->getField('passport');
        if (!$passport) {
            $passport = md5($this->_now . rand(1, 99999));
            M('user_passport')->data(['passport' => $passport, 'user_id' => $user_id, 'add_time' => time(), 'type' => $type])->add();
        }
        return $passport;
    }

    /**
     * 密码加密
     * @access protected
     * @param string $string 需要加密字符串
     * @since 1.0
     * @return string
     */
    protected function passwordEncryption($string)
    {
        return sha1(md5($string));
    }

    /**
     * 检查令牌正确性
     * @access protected
     * @since 1.0
     * @return string
     */
    protected function _checkSign()
    {
        $parameters = $this->_parameters;
        $parameters['token'] = AUTH_KEY;
        ksort($parameters);
        $sign = '';
        foreach ($parameters as $value) {
            $sign .= $value . '&';
        }
        $sign = trim($sign, '&');
        $sys_sign = md5($sign);
        $user_sign = I('sign');
        if ($sys_sign != $user_sign) {
            $this->_returnError(10001, 'sign不合法');
        }
    }

    /**
     * 获取参数
     * @access protected
     * @since 1.0
     * @return string
     */
    protected function _createParameters()
    {
        $parameters = $_GET['parameters'] ? $_GET['parameters'] : urldecode(I('parameters'));
        if (empty($parameters) === true) {
            return [];
        }
        $result = json_decode($parameters, true);
        if (is_array($result)) {
            return $result;
        } else {
            $this->_returnError(10001, 'sign不合法');
        }
    }

    /**
     * 保存短信验证码和接收手机号码
     * @access protected
     * @param array $data 需要保存的数据
     * @since 1.0
     * @return bool
     */
    protected function saveNoteCode($data)
    {
        /*5分钟内有效*/
        cookie('coupon_code_' . $data['mobile'], null);
        cookie('code_' . $data['mobile'], $data, array('expire' => 300, 'prefix' => 'coupon_'));
        return true;
    }

    /**
     * 获取短信验证码和接收手机号码
     * @access protected
     * @since 1.0
     * @return bool
     */
    protected function getNoteCode($mobile)
    {
        return cookie('coupon_code_' . $mobile);
    }

    /**
     * 删除短信验证码和接收手机号码
     * @access protected
     * @since 1.0
     * @return bool
     */
    protected function delNoteCode($mobile)
    {
        cookie('coupon_code_' . $mobile, null);
        return true;
    }


    protected function _returnError($code, $message, $url = '')
    {
        $this->ajaxReturn([
            'error_code' => $code,
            'message' => $message,
            'url' => $url
        ]);
    }

    protected function _returnData($data = [], $message = '操作成功')
    {
        $this->ajaxReturn([
            'error_code' => 0,
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function _curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $info = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Errno' . curl_error($ch);
        }

        curl_close($ch);

        return $info;

    }

    protected function _curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $info = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Errno' . curl_error($ch);
        }

        curl_close($ch);

        return $info;

    }


}