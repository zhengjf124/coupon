<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;


/**
 * 积分记录
 * @author fdx
 * @package Home\Controller
 */
class CreditController extends ApiController
{ // 接口必须继承这个控制器

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();

    }



    /**
     * ##积分列表
     * URI: /Home/Credit/creditList
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     *  _passport |   string    |  必填  |用户票据
     *  offset  |   int    |  必填  |分页开始
     *  limit   |   int    |  必填  |每页几条
     *
     * @return
     * name   | type     | description
     * --------|----------|----------------------
     * list     | array   | 积分明细列表二维数组
     * count    | int     | 总数
     *
     * list:
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | ID
     * credit  | int      | 积分
     * add_time| int      | 生成时间
     * title    | string   | 标题
     * type    | int      | 0：-，1：+
     *
     */
    function creditList()
    {
        $user = $this->_checkPassport();//用户必须登入
        $offset = I('offset',1);
        //$offset -= 1;
        $limit = I('limit',20);
        $where = ["user_id"=>$user['id']];
        $list = M("credit_log")->field(' id  , credit , add_time , title , type')
            ->where($where)->limit($offset,$limit)->order('id desc')->select();
        foreach ($list as &$val) {
            $val['add_time'] = date("Y m-d");
        }

        $data = ['list'=>$list,'count'=>count($list)];
        $this->_returnData($data);
    }

    /**
     * ##当前积分
     * URI: /Home/Credit/credit
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     *  _passport | string |  必填  |用户票据
     *
     * @return
     * name   | type     | description
     * --------|----------|----------------------
     * total_credit| int   | 当前用户总积分
     *
     *
     */
    function credit()
    {
        $user = $this->_checkPassport();//用户必须登入
        $creditLogic = new \Home\Logic\CreditLogic();
        $credit = $creditLogic->getCredit($user['id']);
        $data = ['total_credit'=>$credit];
        $this->_returnData($data);
    }








}//end