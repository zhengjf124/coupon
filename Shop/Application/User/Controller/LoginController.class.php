<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 用户登入注册接口
 * @author wubuze
 * @package Home\Controller
 *
 */
class LoginController extends ApiController
{ // 接口必须继承这个控制器

    public function __construct()
    {
        parent::__construct();
        $this->_url = C("site_url").'/index.php?m=home&c=wxLogin&a=ind&redirect_uri=';

    }

    /**
     * 单独判断是否登入 \n
     * URI : /Home/Login/check
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * passport| string   | 用户票据 （error_code =0 ，成功则返回票据。\n error_code =10001 票据过期了。 error_code = 10000 票据为空 )
     *
     */
    public function check()
    {//单独判断

        $passport = I('_passport');

        if (!$passport) {
            $this->_returnError('10000', 'passport is null', $this->_url);
        }

        $res = M('UserPassport')->where(['passport' => $passport])->find();

        $userInfo = M('User')->field('id,user_name')->where(['id' => $res['user_id']])->find();

        //print_r($userInfo);
        if ($userInfo) {
            $data = ['passport'=>$passport];
            $this->_returnData($data);
        } else {
            $this->_returnError('10001', 'passport is invalid', $this->_url);
        }


    }
    /**
     * 用户注册接口 \n
     * URI : /Home/Login/reg
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     *  user_name  | string  | 必填 |用户名
     *  password  | string   | 必填 |密码
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 用户ID
     *
     */
    public function reg()
    {
        $username = I('user_name');
        if (empty($username)) {
            $this->_returnError('10004', 'user_name is null');
        } else {
            $user = D('User')->checkField('user_name', $username);
            if ($user) {
                $this->_returnError('10002', '用户名已经被注册');
            }
        }
        $password = I('password');
        if (empty($password)) {
            $this->_returnError('10003', 'password is null');
        } else {
            $data['password'] = md5($password);
        }
        $data['user_name'] = $username;
        $id = M("User")->data($data)->add();

        $data = [
            'id' => $id
        ];
        $this->_returnData($data);


    }

    /**
     * 用户登入接口 \n
     * URI : /Home/Login/login
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     *  user_name  | string   | 必填 |用户名
     *  password  | string   | 必填 |密码
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * passport| string      | 用户票据
     *
     */
    public function login()
    {
        $username = I('user_name');
        $password = I('password');

        // 参数判断统一在代码最前面，一方面代码结构更清晰，一方面 你参数都验证没通过，后面代码执行浪费性能
        if (empty($username)) {
            $this->_returnError('10004', 'user_name is null');
        }
        if (empty($password)) {
            $this->_returnError('10003', 'password is null');

        }

        $user = D('User')->checkField('user_name', $username);
        if (!$user) {
            $this->_returnError('10005', '用户不存在');
        }


        if (md5($password) != $user['password']) {
            $this->_returnError('10006', '用户名或密码错误');
        }


        $passport = $this->_createPassport($user);
        $data = [
            'passport' => $passport
        ];
        $this->_returnData($data);


    }




}//end