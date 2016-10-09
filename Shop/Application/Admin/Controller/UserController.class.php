<?php
namespace Admin\Controller;

use Think\AjaxPage;

class UserController extends BaseController
{

    public function index()
    {
        $this->assign('fTitle', '会员中心');
        //$admin = M('User')->field('id,user_name,nick_name,email,reg_time')->select();
        //$this->assign('admin',$admin);
        //print_r($admin);

        $this->display();

    }

    /**
     * 会员列表
     */
    public function ajaxindex()
    {
        // 搜索条件
        $condition = array();
        I('mobile') ? $condition['mobile'] = array('like', '%' . I('mobile') . '%') : false;
        I('email') ? $condition['email'] = I('email') : false;
        $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');

        $model = M('user');
        $count = $model->where($condition)->count();
        $Page = new AjaxPage($count, 10);
        //  搜索条件下 分页赋值
        foreach ($condition as $key => $val) {
            $Page->parameter[$key] = urlencode($val);
        }
        $userList = $model->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('first_leader', 123);
        $this->assign('second_leader', 123);
        $this->assign('third_leader', 123);

        $show = $Page->show();
        $this->assign('userList', $userList);
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    /**
     * 会员详细信息查看
     */
    public function detail()
    {
        $uid = I('get.id');
        $user = D('user')->where(array('id' => $uid))->find();
        if (!$user)
            exit($this->error('会员不存在'));
        if (IS_POST) {
            //  会员信息编辑
            $password = I('post.password');
            $password2 = I('post.password2');
            if ($password != '' && $password != $password2) {
                exit($this->error('两次输入密码不同'));
            }
            if ($password == '' && $password2 == '') {
                unset($_POST['password']);
            } else {
                //$_POST['password'] = encrypt($_POST['password']);
                $_POST['password'] = md5($_POST['password']);
            }
            $cre = M('user')->create();
            // print_r($cre);exit;

            $row = M('user')->where(array('id' => $uid))->save($_POST);
            if ($row === false) {
                exit($this->error('未作内容修改或修改失败'));

            } else {
                exit($this->success('修改成功', U('User/index')));

            }

        }

        // $user['first_lower'] = M('users')->where("first_leader = {$user['user_id']}")->count();
        // $user['second_lower'] = M('users')->where("second_leader = {$user['user_id']}")->count();
        // $user['third_lower'] = M('users')->where("third_leader = {$user['user_id']}")->count();
        //print_r($user);
        $this->assign('user', $user);
        $this->display();
    }

    /**
     * 用户收货地址查看
     */
    public function address()
    {
        $uid = I('get.id');
        $lists = D('user_address')->where(array('id' => $uid))->select();
        // $regionList = M('Region')->getField('id,name');
        $this->assign('regionList', $regionList);
        $this->assign('lists', $lists);
        $this->display();
    }


}