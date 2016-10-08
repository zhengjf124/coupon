<?php
namespace Home\Logic;
/**
 * Created by PhpStorm.
 * User: dzjzmj
 * Date: 16/4/22
 * Time: 下午2:56
 */
class DemoLogic
{
    public function test() {
        return 1;
    }

    public function getList($user_id,$field='')
    {
        if(!$field){
            $field = "id,consignee,province,city,district,address,mobile";
        }
        $data = M('User_address')->where('')->find();
        return $data;
    }
}
