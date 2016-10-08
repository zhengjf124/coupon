<?php
namespace Home\Model;

use Think\Model;

class UserModel extends Model
{


    public function checkField($key, $value)
    {
        return $this->where([$key => $value])->field('user_id,password')->find();
    }

    public function checkField_wx($name, $value)
    {
        return M('WeixinUser')->field('id,nickname,openid')->where([$name => $value])->find();
    }


}//end
