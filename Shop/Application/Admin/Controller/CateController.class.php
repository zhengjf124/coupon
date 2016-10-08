<?php
namespace Admin\Controller;
use Admin\Logic\GoodsLogic;
use Think\AjaxPage;
use Think\Page;
class GoodsController extends BaseController
{   

    public function goodsList()
    {
        $this->assign('fTitle','商品列表');
        $admin = M('User')->field('id,user_name,email,reg_time')->select();
        $this->assign('admin',$admin);
        //print_r($admin);
        
        $this->display();

    }

    public function addEditGoods()
    {
        $Goods = D('Goods');

        $goods_id = $_POST['goods_id'];
        if(($_GET['is_ajax'] == 1) && IS_POST)
        {
            $_POST['add_time'] = time(); //上架时间
            if(!$goods_id)// 根据表单提交的POST数据创建数据对象
            {
                //$Goods->add();
                //$this->success('操作成功',U('Goods/goodsList'));
            }else {
                $_POST['id'] = $goods_id;
                $cre = $Goods->create();
                //print_r($cre);exit;
                $Goods->save(); // 写入数据到数据库
                $Goods->afterSave($goods_id);
                //exit;
                $this->success('操作成功',U('Goods/goodsList'));
            }
            return;

        }


        $goodsStyle = M("GoodsStyle")->select();
        $goodsInfo = M('Goods')->where('id='.I('GET.id',0))->find();
        $this->assign('goodsInfo',$goodsInfo);  // 商品详情
        $this->assign('goodsType',$goodsStyle);
        //print_r($goodsStyle);
        $this->display('_goods');
    }

    public function ajaxGoodsList(){

        $model = M('Goods');
        $where = array();
        $count = $model->where($where)->count();
        $Page  = new AjaxPage($count,10);
        $show = $Page->show();
        $order_str = "";
        $goodsList = $model->where($where)->field('id,goods_name,goods_sn')->order($order_str)->limit($Page->firstRow.','.$Page->listRows)->select();
//print_r($goodsList);exit;
        $this->assign('goodsList',$goodsList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    /**
     * 动态获取商品规格选择框 根据不同的数据返回不同的选择框
     */
    public function ajaxGetSpecSelect(){
        $goods_id = $_GET['goods_id'] ? $_GET['goods_id'] : 0;
        $GoodsLogic = new GoodsLogic();
        //$_GET['spec_type'] =  13;
        $specList = D('Spec')->where("type_id = ".$_GET['spec_type'])->order('`order` desc')->select();
        foreach($specList as $k => $v)
            $specList[$k]['spec_item'] = D('SpecItem')->where("spec_id = ".$v['id'])->getField('id,item'); // 获取规格项

        $items_id = M('SpecGoodsPrice')->where('goods_id = '.$goods_id)->getField("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id");
        $items_ids = explode('_', $items_id);

        // 获取商品规格图片
        if($goods_id)
        {
            $specImageList = M('SpecImage')->where("goods_id = $goods_id")->getField('spec_image_id,src');
        }
        $this->assign('specImageList',$specImageList);

        $this->assign('items_ids',$items_ids);
        $this->assign('specList',$specList);
        $this->display('ajax_spec_select');
    }

    /**
     * 动态获取商品规格输入框 根据不同的数据返回不同的输入框
     */
    public function ajaxGetSpecInput(){
        $GoodsLogic = new GoodsLogic();
        $goods_id = $_REQUEST['goods_id'] ? $_REQUEST['goods_id'] : 0;
        $str = $GoodsLogic->getSpecInput($goods_id ,$_POST['spec_arr']);
        exit($str);
    }



}