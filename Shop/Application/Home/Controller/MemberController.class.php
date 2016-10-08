<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

class MemberController extends ApiController
{
    protected $user_id;   //用户信息
    protected $user_info; //用户ID

    public function __construct()
    {
        parent::__construct();
        $this->user_info = $this->_checkPassport();
        $this->user_id = $this->user_info['user_id'];
    }


}
