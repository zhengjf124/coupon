<?php
namespace Admin\Controller;

use Think\Controller;


class BaseController extends Controller
{ 
    protected $user_id;
    /**
     * 构造函数
     *
    */
    public function __construct() {
        parent::__construct();

        $this->user_id = session('admin_id');
    }

    /*
     * 初始化操作
     */
    public function _initialize()
    {
        $this->assign('action',ACTION_NAME);
        //过滤不需要登陆的行为
        if(in_array(ACTION_NAME,array('login','logout','vertify')) || in_array(CONTROLLER_NAME,array('Ueditor','Uploadify'))){
            //return;
        }else{
            if(session('admin_id') > 0 ){
                $mod_id =  $this->check_priv();//检查管理员菜单操作权限
                //echo $res;exit;
                if($mod_id ==false ){
                    $this->error('请系统管理员先在菜单管理页添加该菜单'.$mod_id,U('Admin/Index/welcome'));
                    exit;
                }else{
                    $act_list = '23,45,34';
                    $act_list = explode(',', $act_list);
                    if(!in_array($mod_id, $act_list)){
                        $this->error('您的账号没有此菜单操作权限,超级管理员可分配权限111'.$mod_id,U('Admin/Index/welcome'));
                        exit;
                    }else{
                        return 1;
                    }
                }



            }else{
                //$this->error('请先登陆',U('Admin/Login/index'),1);
                $this->redirect('Admin/Login/index');
            }
        }

    }

    public function _returnError($errmsg)
    {
        die("<script>alert('".$errmsg."');history.back(-1)</script>");
    }

    public function _returnData($errmsg,$url)
    {
       // die("<script>alert('".$errmsg."');href='".$url."';</script>");
    }

    public function check_priv()
    {
       $ctl = CONTROLLER_NAME;
       $act = ACTION_NAME;
        $act_list = session('act_list');
        $no_check = array('login','logout','vertifyHandle','vertify','imageUp','upload');
        if($ctl == "Index" && $act == 'index' || $act == 'welcome'){
            return true;
        }elseif(strpos('ajax',$act) || in_array($act,$no_check) || $act_list == 'all'){
            return true;
        }else {
            //$mod = M('system_module')->where("ctl='$ctl' and act='$act'")->field('mod_id')->find();
            $mod = M('system_module')->where(['ctl'=>$ctl,'act'=>$act])->field('mod_id')->find();
            $mod_id = $mod['mod_id'];
            //settype($mod_id,'int');
            // var_dump($mod_id);

            return 34;


//            if($mod_id ==false ){
//                //$this->error('请系统管理员先在菜单管理页添加该菜单2',U('Admin/Index/welcome'));
//                $this->error('请系统管理员先在菜单管理页添加该菜单'.$mod_id,U('Admin/Index/welcome'));
//                exit;
//            }else{
//                $act_list = explode(',', $act_list);
//                if(!in_array($mod_id, $act_list)){
//                    $this->error('您的账号没有此菜单操作权限,超级管理员可分配权限111',U('Admin/Index/welcome'));
//                    exit;
//                }else{
//                    return 1;
//                }
//            }


        }






    }

    


   




}//end