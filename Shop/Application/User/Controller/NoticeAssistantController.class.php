<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 订单消息通知
 * @author wubuze
 * @package Home\Controller
 */
class NoticeAssistantController extends ApiController
{ // 接口必须继承这个控制器


    /**
     * 客服助手添加留言 \n
     * URI : /Home/NoticeAssistant/add
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string  | 必须   | 用户票据
     * assistant_id| int  | 必须   | 客服人员id
     * topic    | string  | 必须   | 留言内容
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 留言记录id
     *
     */
    public  function add(){
        $user  = $this->_checkPassport();//用户必须登入
        $userId = $user['id'];
        $assistantId = I("assistant_id");
        $topic = I("topic");

        $add['topic'] = $topic;
        $add['assistant_id'] = $assistantId;
        $add['user_id'] = $userId;
        $add['add_time'] = time();
        $add['type']    = 2; //普通用户留言
        $id = M('NoticeAssistant')->data($add)->add();

        $data = ['id'=>$id];
        $this->_returnData($data);
    }



    /**
     * 客服助手消息列表 \n
     * URI : /Home/NoticeAssistant/index
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string  | 必须   | 用户票据
     * assistant_id| id  | 必须   | 客服id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * topic| string    | 订单通知内容
     * add_time| string   | 时间
     * type    | int      | 1-客服留言    2-用户留言
     *
     */
    public  function index(){
        $user  = $this->_checkPassport();//用户必须登入
        $assistantId = I("assistant_id");

        $where['user_id'] = $user['id'];
        $where['assistant_id'] = $assistantId;
        $info = M('NoticeAssistant')->field('topic,add_time,id,type')->where($where)->order('add_time asc')->select();
        $count = M('NoticeAssistant')->where('id>0')->count();
        $data = ['list'=>$info, 'count'=>$count];
        $this->_returnData($data);
    }

    /**
     * 删除单条留言内容 \n
     * URI : /Home/NoticeOrder/delete
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string   | 必须   | 用户票据
     * id       | int      | 必须   | 留言id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 留言id
     *
     */
    public  function delete(){
        $user  = $this->_checkPassport();//用户必须登入
        $id = I("id");
        $res = M('NoticeAssistant')->where(['id'=>$id, 'user_id'=>$user['id']])->delete();

        $data = $id;
        $this->_returnData($data);
    }

















}//end