<?php
/**
 *
 */

namespace Home\Logic;

use Think\Controller;

/**
 * 分类逻辑定义
 * Class AddressLogic
 * @package Home\Logic
 */
class AddressLogic extends Controller
{

    /**
     * 获取一条收获地址方法
     * @param $id
     * @param $field
     * @return mixed
     */
    function getAddress($id,$field='')
    {
        if(!$field){
            $field = "id,consignee,province,city,district,address,mobile";
        }
        $data = M('UserAddress')->field($field)->where(['id'=>$id])->find();
        return $data;
    }

    public function getList($user_id,$field='')
    {
        if(!$field){
            $field = "id,consignee,province,city,district,address,mobile,is_default";
        }
        $data = M('User_address')->field($field)->where(['user_id'=>$user_id])->select();
        return $data;
    }

    function getDefault($user_id,$field='')
    {
        if(!$field){
            $field = "id,consignee,province,city,district,address,mobile";
        }
        $data = M('UserAddress')->field($field)->where(['user_id'=>$user_id,'is_default'=>1])->find();
        return $data;
    }








}

 