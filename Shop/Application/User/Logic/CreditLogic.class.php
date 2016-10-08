<?php
namespace Home\Logic;
/**
 * Created by notepad++.
 * User: fdx
 * Date: 16/7/25
 * Time: 下午2:56
 */
class CreditLogic
{
    /**
     * 用户获得积分
     * @param int $userId  用户ID
     * @param int $orderId   订单ID
     * @param folat $money  付款金额
     */
    function commentCredit($userId,$orderId,$money)
    {
        
        $info = M("credit")->where(["user_id"=>$userId])->find(); //判断积分表是否存在该用户
        //return array('status'=>1,'msg'=>'成功添加积分','user_id'=>$info);
        if(!$info) {
            M("credit")->data(["user_id"=>$userId])->add();
        }
        
        $rule = M("credit_rule")->where(["id"=>1])->getField('credit');//积分规则
        $addCredit = $money * $rule * 0.01;   //获得积分
        $data['user_id']    = $userId;
        $data['credit_type']= 1;
        $data['credit']     = $addCredit;
        $data['add_time']   = time();
        $data['note']       = '订单：'.$orderId.'获得积分：'.$addCredit;
        $data['type']       = 1;
        M("credit_log")->add($data);//生成记录
        
        M("credit")->where(["userId"=>$userId])->setInc('total_credit',$addCredit);//增加积分
        
        return array('status'=>1,'msg'=>'成功添加积分');
       
    }
    
    
    /**
     * 抵用积分
     * @param int $userId  用户ID
     * @param int $orderId   订单ID
     * @param folat $credit  抵用积分
     */
    function useCredit($userId,$orderId,$credit)
    {
        $getCredit = $this->getCredit($userId);//获取用户积分
        if($credit > $getCredit) return array("status"=>30013,'msg'=>'用户积分不足');//判断积分是否足够
        
        $data['user_id']    = $userId;
        $data['credit_type']= 0;
        $data['credit']     = $credit;
        $data['add_time']   = time();
        $data['note']       = '订单：'.$orderId.'使用积分：'.$credit;
        $data['title']      = '评论商品';
        $data['type']       = 0;
        M("credit_log")->add($data);//生成记录
        
        M("credit")->where(["userId"=>$userId])->setDec('total_credit',$credit);//扣除积分
        
        return array('status'=>1,'msg'=>'成功抵用积分');
    }
    
    
    /**
     * 获取当前用户积分
     * @param int $userId  用户ID
     */
    function getCredit($userId)
    {
        $info = M("credit")->where(["user_id"=>$userId])->find(); //判断积分表是否存在该用户
        if(!$info) {
            M("credit")->add(["user_id"=>$userId]);
            $info['total_credit']=0;
        }
        
        return $info['total_credit'];
    }
    
    
    /**
     * 最大使用量(暂时不用)
     * @param int $userId  用户ID
     * @param folat $money  付款金额
     */
    function judgeCredit($userId,$money)
    {
        $rule = M("credit_rule")->where(["id"=>2])->getField('credit');//积分规则
        $maxCredit = $money * $rule * 0.01;   //积分使用比例
        
        return $maxCredit;
        
    }
    
    

    









}
