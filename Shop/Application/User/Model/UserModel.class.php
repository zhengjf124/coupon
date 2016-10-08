<?php
namespace Home\Model;
use Think\Model;

class UserModel extends Model
{ 

   
    public function checkField($name,$value)
    {

       $user = $this->field('id,user_name,password')->where([$name => $value])->find();
       return $user;
    }

    public function checkField_wx($name,$value)
    {

        $user = M('WeixinUser')->field('id,nickname,openid')->where([$name => $value])->find();
        return $user;
    }


     




   
}//end