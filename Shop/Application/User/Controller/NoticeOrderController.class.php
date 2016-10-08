<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 订单消息通知
 * @author wubuze
 * @package Home\Controller
 */
class NoticeOrderController extends ApiController
{ // 接口必须继承这个控制器

    /**
     * 订单通知消息列表 \n
     * URI : /Home/NoticeOrder/index
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string  | 必须   | 用户票据
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * title   | string    | 通知标题
     * content| string    | 订单通知内容
     * add_time| string   | 时间
     * order_id| int   | 订单id
     *
     */
    public  function index(){
        $user  = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        $info = M('NoticeOrder')->field('title,content,add_time,order_id')->where(['user_id'=>$userId])->order('add_time desc')->select();
        foreach($info as & $val){
            $val['add_time'] = date('Y-m-d',$val['add_time']);
        }
        $count = M('NoticeOrder')->where(['user_id'=>$userId])->count();

        $data = ['list'=>$info, 'count'=>$count];
        $this->_returnData($data);
    }


    /**
     * 优惠促销消息列表 \n
     * URI : /Home/NoticeOrder/sales
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string  | 必须   | 用户票据
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * content| string    | 订单通知内容
     * add_time| string   | 时间
     * id      | int      | 活动id
     *
     */
    public  function sales(){

        $info = M('NoticeSales')->field('title,content,add_time,id')->where('id>0')->order('add_time desc')->select();
        if($info){
            foreach ($info as &$val){
                $val['add_time'] = date('Y-m-d',$val['add_time']);
            }
        }
        $count = M('NoticeSales')->where('id>0')->count();
        $data = ['list'=>$info, 'count'=>$count];
        $this->_returnData($data);
    }

    /**
     * 优惠促销消息详情 \n
     * URI : /Home/NoticeOrder/sales_detail
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string   | 必须   | 用户票据
     * id       | int      | 必须   | 活动id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * content | string    | 订单通知内容
     * add_time| string   | 时间
     * id      | int      | 活动id
     *
     */
    public  function sales_detail(){
        $id = I("id");

        $info = M('NoticeSales')->field('title,content,add_time,id')->where('id='.$id)->find();
        if(!$info){
            $this->_returnError('1','活动不存在');
        }
        $data = $info;
        $this->_returnData($data);
    }


    /**
     * 积分消息列表 \n
     * URI : /Home/NoticeOrder/NoticeIntegral
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string  | 必须   | 用户票据
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * title   | string    | 通知标题
     * note    | string    | 通知内容
     * add_time| string   | 时间
     *
     */
    public  function NoticeIntegral(){
        $user  = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        $info = M('CreditLog')->field('title,note,add_time')->where(['user_id'=>$userId])->order('add_time desc')->select();
        foreach($info as & $val){
            $val['add_time'] = date('Y-m-d',$val['add_time']);
        }
        $count = M('NoticeOrder')->where(['user_id'=>$userId])->count();
        $data = ['list'=>$info, 'count'=>$count];
        $this->_returnData($data);
    }














}//end