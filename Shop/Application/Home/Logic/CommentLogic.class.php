<?php
/**
 * @author wbz
 */

namespace Home\Logic;

use Think\Controller;
/**
 * 评论逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */
class CommentLogic extends Controller
{
    /**
     * 判断商品是否已经评论
     *
     */
    public function checkGoodsIsCommented($userId,$goodsId,$orderId)
    {

        $goods = M('OrderGoods')->where(['order_id'=>$orderId, 'goods_id'=>$goodsId])->find();
        if(!$goods){
            return array('status'=>1,'msg'=>'评论对象不存在');
        }

        $data = M('Comments')->where(['user_id'=>$userId, 'goods_id'=>$goodsId])->find();
        if($data){
            return array('status'=>2,'msg'=>'已经评论过了');
        }else{
            return array('status'=>0,'msg'=>'');
        }
    }

    /**
     * 添加评论
     *
     */
    public function addComment($data,$orderId,$goodsId)
    {
        $id = M('Comments')->data($data)->add();
        if(!$id){
            return array('status'=>1,'msg'=>'评论失败');
        }else{
            //订单商品评论状态
            M("OrderGoods")->where(["goods_id"=>$goodsId,'order_id'=>$orderId])->setField('is_comment',1);

            //本订单的所有商品都评论以后，改变订单状态为结束 订单状态：100-待付款；200-待发货；101-已取消；210-待收货；220-待评价；222-完成
            $goods = M('OrderGoods')->where(["is_comment"=>0,'order_id'=>$orderId])->find();
            if(!$goods){
                M('Order')->where(['id'=>$orderId])->setField('order_status',222);
            }
            return array('status'=>0,'msg'=>'','id'=>$id);
        }
    }



    


}//end

 