<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * 会员管理
 * @author dexin wbz
 * @package Admin\Controller
 */
class LoginController extends Controller
{

    /**
     * 管理员登陆
     *
     */
    public function login()
    {
        if(IS_POST){

            $code = I("code");
            if( !$this->check_verify( $code ) )
            {
                $this->_returnError(2,'验证码错误','');
            }
            $condition['user_name'] = I('post.username');
            $condition['password'] = I('post.password');
            if(!empty($condition['user_name']) && !empty($condition['password'])){
                $condition['password']  = md5(md5($condition['password']));
                $admin_info = M('admin')->join('__ADMIN_ROLE__ ON __ADMIN__.role_id=__ADMIN_ROLE__.role_id')->where($condition)->find();
                //$admin_info = M('Admin')->where(array('user_name'=>$username))->find();
                session('act_list',$admin_info['act_list']);
                session('admin_id',$admin_info['id']);
                $data["last_login"] = time();
                $data["last_ip"] = get_client_ip();
                adminLog('后台登录',__ACTION__);
                M("Admin")->where(array("id"=>$admin_info['id']))->save($data);
                //print_r($admin_info);exit;
                //$admin_info = json_encode($admin_info);
                $url = session('from_url') ? session('from_url') : U('Admin/Index/index');
                if($admin_info){
                    exit(json_encode(array('status'=>1,'msg'=>'登入成功','url'=>$url)));
                    //$this->_returnError(1,'登入成功',U('Admin/Index/index'));
                }else{
                    exit(json_encode(array('status'=>0,'msg'=>'账号密码错误')));
                }
            }else{
                exit(json_encode(array('status'=>0,'msg'=>'账号密码错误')));
            }





        }

    }


    /*
     * 管理员登陆
     */
    public function login222(){
        if(session('?admin_id') && session('admin_id')>0){
            $this->error("您已登录",U('Admin/Index/index'));
        }

        if(IS_POST){
            $verify = new Verify();
            if (!$verify->check(I('post.vertify'), "Admin/Login")) {
                exit(json_encode(array('status'=>0,'msg'=>'验证码错误')));
            }
            $condition['user_name'] = I('post.username');
            $condition['password'] = I('post.password');
            if(!empty($condition['user_name']) && !empty($condition['password'])){
                $condition['password'] = encrypt($condition['password']);
                $admin_info = M('admin')->join('__ADMIN_ROLE__ ON __ADMIN__.role_id=__ADMIN_ROLE__.role_id')->where($condition)->find();
                if(is_array($admin_info)){
                    session('admin_id',$admin_info['admin_id']);
                    session('act_list',$admin_info['act_list']);
                    $last_login_time = M('admin_log')->where("admin_id = ".$admin_info['admin_id']." and log_info = '后台登录'")->order('log_id desc')->limit(1)->getField('log_time');
                    session('last_login_time',$last_login_time);
                    adminLog('后台登录',__ACTION__);
                    $url = session('from_url') ? session('from_url') : U('Admin/Index/index');
                    exit(json_encode(array('status'=>1,'aaaaa','url'=>$url)));
                }else{
                    exit(json_encode(array('status'=>0,'msg'=>'账号密码不正确')));
                }
            }else{
                exit(json_encode(array('status'=>0,'msg'=>'请填写账号密码')));
            }
        }

        $this->display();
    }

    public  function logout()
    {
        session('admin_id',null);
        $this->redirect('Login/index');
    }


    public function _returnError($status,$msg,$url)
    {
        //die("<script>alert('".$errmsg."');history.back(-1)</script>");
        $data['status'] = $status;
        $data['msg'] = $msg;
        $data['url'] = $url;
        $this->ajaxReturn($data);
    }






    //验证码生成
    public function verify()
    {  
        $Verify = new \Think\Verify();  
        $Verify->fontSize = 12;  
        $Verify->length   = 1;  
        $Verify->useNoise = false;  
        $Verify->codeSet = '1234567890';  
        $Verify->imageW = 100;  
        $Verify->imageH = 30;  
        //$Verify->expire = 600;  
        $Verify->entry();  
    } 

    //验证码验证
    function check_verify($code, $id = "")
    {
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }  






}//end