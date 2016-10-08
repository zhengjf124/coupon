<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 评论接口
 * @author fangdexin  wbz修改
 * @package Home\Controller
 */
class CommentsController extends ApiController
{

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();

    }

    
    /**
     * ##用户提交评论
     * URI : /Home/Comments/add
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport | string  | 必填 |用户票据
     * goods_id  | int  |   必填  | 商品ID
     * order_id  | int  |   必填  | 订单ID
     * content   | string  |  必填  | 评价内容
     * comment_img| json  |  选填  | 评价图片数组  comment_img:[img1,img2,img3]
     * lv_goods   | int  |  必填  | 评价描述分数
     * lv_speed  | int  |  必填  | 评价速度分数
     * lv_server | int  |  必填  | 评价等级分数
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     *  id     | int      | 评论id
     *
     *
     */
    public function add()
    {
        $user = $this->_checkPassport();//用户必须登入
        
        $userId     = $user['id'];
        $userName   = $user['user_name'];
        $email      = $user['email'];
        $goodsId    = I("goods_id");//每次评论一个商品
        $orderId    = I("order_id");//
        $content     = I("content",'');
        $comment_img   = I("comment_img",'');
        $lv_goods     = I("lv_goods") ? I("lv_goods"):5;
        $lv_speed    = I("lv_speed") ? I("lv_speed"):5;
        $lv_server   = I("lv_server") ? I("lv_server"):5;

        if(empty($goodsId) || empty($content)  || empty($orderId)  ){
            $this->_returnError('90001','请求参数不完整');
        }
        $comments = new \Home\Logic\CommentLogic();
        $is_comment_res = $comments->checkGoodsIsCommented($userId,$goodsId,$orderId);


        if($is_comment_res['status']==1){
            $this->_returnError('90003','评论商品不存在');
        }
        if($is_comment_res['status']==2){
            $this->_returnError('90002','商品已经评论过了');
        }


        $record['user_id']   = $userId;
        $record['user_name'] = $userName;
        $record['email']     = $email;
        $record['add_time'] = time();
        if($comment_img){
            $record['comment_img'] = serialize($comment_img); // 上传的图片文件

        }
        $record['goods_id']  = $goodsId;           //评价商品ID
        $record['order_id']  = $orderId;           //订单id
        $record['content']   = $content;            //评价内容
        $record['lv_goods']   = $lv_goods;            //描述相符
        $record['lv_speed']  = $lv_speed;           //发货速度
        $record['lv_server'] = $lv_server;          //服务态度

        $res = $comments->addComment($record,$orderId,$goodsId);

        if($res['status']>0)
        {
            $this->_returnError('90003','评论失败');
        }
        //评论积分
        $goodsPrice = M('orderGoods')->where(['goods_id'=>$goodsId,'order_id'=>$orderId])->getField('goods_price');
        $creditLogic = new \Home\Logic\CreditLogic();
        $creditLogic->commentCredit($userId,$orderId,$goodsPrice);

        $data = ['id'=>$res['id']];
        $this->_returnData($data);
        
    }

    /**
     * ##评论晒图上传单独接口
     * URI : /Home/Comments/uploadCommentImg
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     * comment_img_file| file资源  | 必填 |图片  支持批量上传 （json格式 comment_img_file:[file1，file2，file3]）
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * imgUrl  | string   | 图片路径
     *
     */
    public function uploadCommentImg(){
        header("Access-Control-Allow-Headers: X-Requested-With");
        $this->_checkPassport();//用户必须登入
        //$user_id = $res['id'];
        //print_r($_FILES);
        if($_FILES[comment_img_file][tmp_name][0])
        {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =    $map['author'] = (1024*1024*3);// 设置附件上传大小 管理员10M  否则 3M
            $upload->exts      =    array('jpg', 'gif', 'png', 'jpeg','bmp');// 设置附件上传类型
            $upload->rootPath  =    './Public/upload/comment/'; // 设置附件上传根目录
            $upload->replace   =    true; // 存在同名文件是否是覆盖，默认为false
            //$upload->saveName  =  'file_'.$id; // 存在同名文件是否是覆盖，默认为false
            // 上传文件
            $upinfo  =  $upload->upload();

            if(!$upinfo) {// 上传错误提示错误信息
                //$this->error($upload->getError());
                $this->_returnError('80003','文件格式不支持');
            }else{
                $comment_imgs = [];
                foreach($upinfo as $val){
                    $comment_img = '/Public/upload/comment/'.$val['savepath'].$val['savename'];
                    $comment_imgs[] = $comment_img; // 上传的图片文件
                }
                $this->_returnData(['imgUrl'=>$comment_imgs]);
            }

        }else{
            $this->_returnError('80003','图片资源不存在');
        }
    }
    
    /**
     * ##商品详情评论列表
     * URI : /Home/Comments/goods_comments
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     *  goods_id |   int    |  必填  |商品ID
     *  offset  |   int    |  选填  |分页开始
     *  limit   |   int    |  选填  |每页几条
     *
     * @return
     * name   | type     | description
     * --------|----------|----------------------
     * list     | array   | 评价列表二维数组
     * count    | int     | 评价总数
     * total_score| int   | 所有评论的总分（星星数量）
     *
     * list:
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 评论ID
     * add_time| int      | 评论时间
     * content | string   | 评论内容
     * user_name | string  | 评论用户
     * headimgurl | string  | 用户头像
     * lv_goods   | int     | 评论星级 （1-5颗星）
     * comment_img     | array   | 评论图片（数组）
     *
     */
    public function goods_comments(){
        
        $goodsId = I("goods_id");
        $offset = I('offset') ? I('offset') : 1;
        $offset = $offset-1;
        $limit = I('limit') ? I('limit')  : 20;
        
        $where = ['goods_id'=>$goodsId , 'status'=>1];
        
        //$count = M('Comments')->where($where)->count();
        $list = M('Comments')->where($where)
                ->field(' id , user_id , user_name , add_time , content ,lv_goods, comment_img')
                ->limit($offset,$limit)->order('id desc')->select();
        foreach($list as &$val){
            $val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
            $img = unserialize($val['comment_img']);
            if($img){
                $val['comment_img'] = $img;
            }else{
                $val['comment_img'] = [];
            }
        }
        $total_score = 0;
        foreach($list as & $val)
        {
            $val['headimgurl'] = M("user")->where(["id"=>$val['user_id']])->getField('headimgurl');
            if(!$val['headimgurl']){
                $val['headimgurl'] = C('site_url').'/Public/weixin/img-tx.jpg';
            }
            $total_score += $val['lv_goods'];
        }
        $count = count($list);
        if($count){
            $total_score  = ceil($total_score/$count);
        }

        $data = ['list'=>$list,'count'=>count($list),'total_score'=>$total_score];
        
        $this->_returnData($data);
    }






   
}//end