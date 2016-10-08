<?php
/**
 * 后台权限管理
 * @author wubuze
 */
namespace Admin\Controller;
class AdminController extends BaseController
{

    /**
     * 管理员列表
     */
    public function index(){
        $res = $list = array();
        $keywords = I('keywords');
        if(empty($keywords)){
            $res = D('admin')->select();
        }else{
            $res = D()->query("select * from __PREFIX__admin where user_name like '%$keywords%' order by id");
        }
        $roles = D('admin_role')->select();
        if($res && $roles){
            foreach ($roles as $v){
                $role[$v['role_id']] = $v['role_name'];
            }
            foreach ($res as $val){
                $val['role'] =  $role[$val['role_id']];
                $val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
                $list[] = $val;
            }
        }
        $this->assign('list',$list);
        //print_r($list);
        $this->display();
    }

    public function admin_info(){
        $admin_id = I('get.admin_id',0);
        if($admin_id){
            $info = D('admin')->where("id=$admin_id")->find();
            $this->assign('info',$info);
        }
        $act = empty($admin_id) ? 'add' : 'edit';
        $this->assign('act',$act);
        $role = D('admin_role')->where('1=1')->select();
        $this->assign('role',$role);
        $this->display();
    }

    public function adminHandle(){
        $data = I('post.');
        if(empty($data['password'])){
            unset($data['password']);
        }else{
            //$data['password'] = encrypt($data['password']);
            $data['password'] = md5(md5($data['password']));
        }
        if($data['act'] == 'add'){
            unset($data['id']);
            $data['add_time'] = time();
            if(D('admin')->where("user_name='".$data['user_name']."'")->count()){
                $this->error("此用户名已被注册，请更换",U('Admin/Admin/admin_info'));
            }else{
                $r = D('admin')->add($data);
            }
        }

        if($data['act'] == 'edit'){
            $r = D('admin')->where('id='.$data['id'])->save($data);
        }

        if($data['act'] == 'del' && $data['id']>1){
            $r = D('admin')->where('id='.$data['id'])->delete();
            exit(json_encode(1));
        }

        if($r){
            $this->success("操作成功",U('Admin/Admin/index'));
        }else{
            $this->error("操作失败",U('Admin/Admin/index'));
        }
    }

    public function role(){
        $list = D('admin_role')->order('role_id desc')->select();
        $this->assign('list',$list);
        //print_r($list);
        $this->display();
    }

    public function role_info(){
        $role_id = I('get.role_id');
        $tree = $detail = array();
        if($role_id){
            $detail = D('admin_role')->where("role_id=$role_id")->find();
            $this->assign('detail',$detail);
        }

        $res = D('system_module')->order('mod_id ASC')->select();
        if($res){
            foreach($res as $k=>$v){
                if($detail['act_list']){
                    $act_list = explode(',', $detail['act_list']);
                    $v['enable'] = in_array($v['mod_id'], $act_list) ? 1 : 0;
                }else{
                    $v['enable'] = 0 ;
                }
                $modules[$v['mod_id']] = $v;
            }

            if($modules){
                foreach($modules as $k=>$v){
                    if($v['module'] == 'top'){
                        $tree[$k] = $v;
                    }
                }
                foreach($modules as $k=>$v){
                    if($v['module'] == 'menu'){
                        $tree[$v['parent_id']]['menu'][$k] = $v;
                    }
                }
                foreach($modules as $k=>$v){
                    if($v['module'] == 'module'){
                        $ppk = $modules[$v['parent_id']]['parent_id'];
                        $tree[$ppk]['menu'][$v['parent_id']]['menu'][$k] = $v;
                    }
                }
            }
        }

        $this->assign('menu_tree',$tree);
        $this->display();
    }

    public function roleSave(){
        $data = I('post.');
        $res = $data['data'];
        $res['act_list'] = is_array($data['menu']) ? implode(',', $data['menu']) : '';
        if(empty($data['role_id'])){
            $r = D('admin_role')->add($res);
        }else{
            $r = D('admin_role')->where('role_id='.$data['role_id'])->save($res);
        }
        if($r){
            adminLog('管理角色',__ACTION__);
            $this->success("操作成功!",U('Admin/Admin/role_info',array('role_id'=>$data['role_id'])));
        }else{
            $this->success("操作失败!",U('Admin/Admin/role'));
        }
    }


    public function log(){
        $Log = M('admin_log');
        $p = I('p',1);
        $logs = $Log->join('__ADMIN__ ON __ADMIN__.id =__ADMIN_LOG__.admin_id')->order('log_time DESC')->page($p.',20')->select();
        $this->assign('list',$logs);
        $count = $Log->where('1=1')->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }







}