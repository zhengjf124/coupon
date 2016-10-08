<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 商品浏览记录接口
 * @author zjf
 * @package Home\Controller
 */
class VisitController extends ApiController
{


    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();

    }


    /**
     * 添加商品浏览记录 \n
     * URI : /
     * @param :
     *   name   |   type   |  null |description
     * ---------|----------|-------|-------------
     * _passport|  string  | 必填  |用户票据
     * goods_id |   int    | 必填  |商品id
     *
     * @return
     *   name  |   type   |  description
     * --------|----------|----------------------
     *
     *
     */
    public function addGoodsLog()
    {
        $res = $this->_checkPassport();//用户必须登入
        //$res['id']
        $log_count = M('goods_favorite')->where(['user_id' => $res['id']])->count();

        $data['goods_id'] = I('goods_id');//商品ID
        $is_log = M('goods_favorite')->where(['user_id' => $res['id'], 'goods_id' => $data['goods_id']])->field('id')->find();
        if ($is_log) { //该商品已经保存了
            $this->_returnData(['id' => $data['goods_id']]);
        }
        $data['user_id'] = $res['id'];//用户ID
        $data['add_time'] = time();//添加时间
        $data['type_id'] = M('goods')->where(['id' => $data['goods_id']])->getField('cat_id');//商品分类ID
        $result = M('goods_favorite')->add($data);
        if ($result) {
            if ($log_count >= 10) { //最多存十条
                $delete_id = M('goods_favorite')->where(['user_id' => $res['id']])->order('id asc')->getField('id');//获取最早的一条记录的ID
                M('goods_favorite')->where(array('id' => $delete_id))->delete(); //删除最早的一条记录
            }
            $this->_returnData(['id' => $data['goods_id']]);
        } else {
            $this->_returnError();
        }
    }

    public function test(){
        $visitLogic = new \Home\Logic\visitLogic();
    }









   
}//end