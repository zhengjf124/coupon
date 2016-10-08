<?php
/**
 * 
 *
 */
namespace Admin\Model;
use Think\Model;

class GoodsModel extends Model {

    
    /**
     * 后置操作方法
     * 自定义的一个函数 用于数据保存后做的相应处理操作, 使用时手动调用
     * @param int $goods_id 商品id
     */
    public function afterSave($goods_id)
    {
        // 商品图片相册  图册
        if(count($_POST['goods_images']) > 1)
        {
            array_unshift($_POST['goods_images'],$_POST['original_img']); // 商品原始图 默认为 相册第一张图片
            array_pop($_POST['goods_images']); // 弹出最后一个
            $goodsImagesArr = M('GoodsImages')->where("goods_id = $goods_id")->getField('img_id,image_url'); // 查出所有已经存在的图片

            // 删除图片
            foreach($goodsImagesArr as $key => $val)
            {
                if(!in_array($val, $_POST['goods_images']))
                    M('GoodsImages')->where("img_id = {$key}")->delete(); // 删除所有状态为0的用户数据
            }
            // 添加图片
            foreach($_POST['goods_images'] as $key => $val)
            {
                if($val == null)  continue;
                if(!in_array($val, $goodsImagesArr))
                {
                    $data = array(
                        'goods_id' => $goods_id,
                        'image_url' => $val,
                    );
                    M("GoodsImages")->data($data)->add(); // 实例化User对象
                }
            }
        }
        // 商品规格价钱处理
        if($_POST['item'])
        {
            $spec = M('Spec')->getField('id,name'); // 规格表
            $specItem = M('SpecItem')->getField('id,item');//规格项

            $specGoodsPrice = M("SpecGoodsPrice"); // 实例化 商品规格 价格对象
            $specGoodsPrice->where('goods_id = '.$goods_id)->delete(); // 删除原有的价格规格对象
            //print_r($_POST['item']);exit;
            foreach($_POST['item'] as $k => $v)
            {
                // 批量添加数据
                $v['price'] = trim($v['price']);
                $store_count = $v['store_count'] = trim($v['store_count']); // 记录商品总库存
                $v['bar_code'] = trim($v['bar_code']);
                $dataList[] = array('goods_id'=>$goods_id,'key'=>$k,'key_name'=>$v['key_name'],'price'=>$v['price'],'store_count'=>$v['store_count'],'bar_code'=>$v['bar_code']);
            }
            $specGoodsPrice->addAll($dataList);

        }
        // 商品规格图片处理
//        if($_POST['item_img'])
//        {
//            M('SpecImage')->where("goods_id = $goods_id")->delete(); // 把原来是删除再重新插入
//            foreach ($_POST['item_img'] as $key => $val)
//            {
//                M('SpecImage')->data(array('goods_id'=>$goods_id ,'spec_image_id'=>$key,'src'=>$val))->add();
//            }
//        }
        refresh_stock($goods_id); // 刷新商品库存


    }



}
