<?php
/**
 *
 */

namespace Home\Logic;

use Think\Model\RelationModel;
/**
 * 分类逻辑定义
 * Class CartLogic
 * @package Home\Logic
 */
class CartLogic extends RelationModel
{

    /**
     * 加入购物车方法
     * @param type $goods_id  商品id
     * @param type $goods_num   商品数量
     * @param type $goods_spec  选择规格
     * @param type $user_id 用户id
     */
    function addCart($goods_id,$goods_num,$goods_spec,$user_id = 0,$buy_type)
    {
        $goods = M('Goods')->field('id,store_count,goods_sn,goods_name,market_price')->where("id = $goods_id")->find(); // 找出这个商品
        $specGoodsPriceList = M('SpecGoodsPrice')->where("goods_id = $goods_id")->getField("key,key_name,price,store_count,bar_code"); // 获取商品对应的规格价钱 库存 条码

        if($user_id)
            $where = ['user_id' => $user_id ];
        $catr_count = M('Cart')->where($where)->count(); // 查找购物车商品总数量

        if($catr_count >= 20)
            return array('status'=>'50010','msg'=>'购物车最多只能放20种商品','result'=>'');
        if($goods_num <= 0)
            return array('status'=>'50011','msg'=>'购买商品数量不能为0','result'=>'');
        if(empty($goods))
            return array('status'=>'50012','msg'=>'购买商品不存在','result'=>'');


        if(!$specGoodsPriceList &&  $goods['store_count']< $goods_num){
            return array('status'=>'50013','msg'=>'商品库存不足','result'=>'');
        }

        if($goods['prom_type'] > 0 && $user_id == 0)
            return array('status'=>'50014','msg'=>'购买活动商品必须先登录','result'=>'');
        if(!empty($specGoodsPriceList) && empty($goods_spec)) // 有商品规格 但是前台没有传递过来
            return array('status'=>'50015','msg'=>'必须传递商品规格','result'=>'');

        //限时抢购 不能超过购买数量
        //delete


        foreach($goods_spec as $key => $val) // 处理商品规格
        {
            $spec_item[] = $val; // 所选择的规格项
        }

        if(!empty($spec_item)) // 有选择商品规格
        {
            sort($spec_item);
            $spec_key = implode('_', $spec_item);

            if($specGoodsPriceList[$spec_key]['store_count'] < $goods_num)
                return array('status'=>'50013','msg'=>'商品库存不足','result'=>'');
            $spec_price = $specGoodsPriceList[$spec_key]['price']; // 获取规格指定的价格

        }



        $where = " goods_id = $goods_id and spec_key = '$spec_key' "; // 查询购物车是否已经存在这商品
        if($user_id > 0){
            //$where .= " and (session_id = '$session_id' or user_id = $user_id) ";
            $where .= " and ( user_id = $user_id) ";
        }
        else{
            //$where .= " and  session_id = '$session_id' ";
            return array('status'=>'10000','msg'=>'需要登入','result'=>'');
        }


        $catr_goods = M('Cart')->where($where)->find(); // 查找购物车是否已经存在该商品
        $price = $spec_price ? $spec_price : $goods['prom_type']; // 如果商品规格没有指定价格则用商品原始价格
        // 商品参与促销




        $data = array(
            'user_id'         => $user_id,   // 用户id
          //  'session_id'      => $session_id,   // sessionid
            'goods_id'        => $goods_id,   // 商品id
            'goods_sn'        => $goods['goods_sn'],   // 商品货号
            'goods_name'      => $goods['goods_name'],   // 商品名称
            'market_price'    => $goods['market_price'],   // 市场价
            'goods_price'     => $price,  // 购买价

            'goods_num'       => $goods_num, // 购买数量
            'spec_key'        => "{$spec_key}", // 规格key
            'spec_key_name'   => "{$specGoodsPriceList[$spec_key]['key_name']}", // 规格 key_name
           // 'bar_code'        => "{$specGoodsPriceList[$spec_key]['bar_code']}", // 商品条形码
           // 'add_time'        => time(), // 加入购物车时间
            //'prom_type'       => $goods['prom_type'],   // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
            //'prom_id'         => $goods['prom_id'],   // 活动id
        );

        // 如果商品购物车已经存在
        if($catr_goods)
        {
            if($buy_type=='buying'){//立即购买
                $result = M('Cart')->where("id =".$catr_goods['id'])->save(  array("goods_num"=>$goods_num) ); //更新数量

            }else{//
                $result = M('Cart')->where("id =".$catr_goods['id'])->save(  array("goods_num"=> ($catr_goods['goods_num'] + $goods_num)) ); // 数量相加

            }
            $cart_count = $this->cart_goods_num($user_id); // 查找购物车数量
            //setcookie('cn',$cart_count,null,'/');

            return array('status'=>0,'msg'=>'成功加入购物车','result'=>$cart_count,'cart_id'=>$catr_goods['id']);
        }
        else
        {
            $cart_id = $insert_id = M('Cart')->add($data);
            $cart_count = $this->cart_goods_num($user_id); // 查找购物车数量
            //setcookie('cn',$cart_count,null,'/');
            return array('status'=>0,'msg'=>'成功加入购物车','result'=>$cart_count,'cart_id'=>$cart_id);
        }

        $cart_count = $this->cart_goods_num($user_id); // 查找购物车数量
        return array('status'=>'50011','msg'=>'加入购物车失败','result'=>$cart_count,'cart_id'=>0);
    }


    function cart_goods_num($user_id = 0)
    {
        $where = ['user_id'=>$user_id];

        // 查找购物车数量
        $cart_count =  M('Cart')->where($where)->sum('goods_num');
        $cart_count = $cart_count ? $cart_count : 0;
        return $cart_count;
    }

    //立即购买获取商品价格
    function buying_cart($goods_spec,$goods_num,$goods_id){

        //$spec_price = 0;

        $specGoodsPriceList = M('SpecGoodsPrice')->where("goods_id = $goods_id")->getField("key,key_name,price,store_count,bar_code"); // 获取商品对应的规格价钱 库存 条码

        foreach($goods_spec as $key => $val) // 处理商品规格
        {
            $spec_item[] = $val; // 所选择的规格项
        }

        if(!empty($spec_item)) // 有选择商品规格
        {
            sort($spec_item);
            $spec_key = implode('_', $spec_item);

            if($specGoodsPriceList[$spec_key]['store_count'] < $goods_num)
                return array('status'=>'50013','msg'=>'商品库存不足','result'=>'');
            $spec_price = $specGoodsPriceList[$spec_key]['price']; // 获取规格指定的价格
            $spec_key_name =  $specGoodsPriceList[$spec_key]['key_name'];
        }

        return ['spec_price'=>$spec_price, 'spec_key_name'=>$spec_key_name];

    }



}

 