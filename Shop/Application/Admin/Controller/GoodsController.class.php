<?php
namespace Admin\Controller;

use Admin\Logic\GoodsLogic;
use Think\AjaxPage;
use Think\Page;

class GoodsController extends BaseController
{
    /**
     *  商品分类列表
     */
    public function categoryList()
    {
        $GoodsLogic = new GoodsLogic();
        $cat_list = $GoodsLogic->goods_cat_list();
        //print_r($cat_list);
        $this->assign('cat_list', $cat_list);
        $this->display();
    }

    /**
     * 添加修改商品分类
     */
    public function addEditCategory()
    {
        $GoodsLogic = new GoodsLogic();
        if (IS_GET) {
            $goods_category_info = D('GoodsCategory')->where('id=' . I('GET.id', 0))->find();
            $level_cat = $GoodsLogic->find_parent_cat($goods_category_info['id']); // 获取分类默认选中的下拉框

            $cat_list = M('goods_category')->where("parent_id = 0")->select(); // 已经改成联动菜单
            $this->assign('level_cat', $level_cat);
            $this->assign('cat_list', $cat_list);
            $this->assign('goods_category_info', $goods_category_info);
            $this->display('_category');

        }
        $GoodsCategory = D('GoodsCategory'); //
        $type = $_POST['id'] > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新

        if (1 == $_GET['is_ajax']) {
            if (!$GoodsCategory->create(NULL, $type)) {
                $return_arr = array(
                    'status' => -1,
                    'msg' => '操作失败',
                    'data' => $GoodsCategory->getError(),
                );
                $this->ajaxReturn(json_encode($return_arr));
            }


            $GoodsCategory->parent_id = $_POST['parent_id_1'];
            $_POST['parent_id_2'] && ($GoodsCategory->parent_id = $_POST['parent_id_2']);

            if ($GoodsCategory->id > 0 && $GoodsCategory->parent_id == $GoodsCategory->id) {
                //  编辑
                $return_arr = array(
                    'status' => -1,
                    'msg' => '上级分类不能为自己',
                    'data' => '',
                );
                $this->ajaxReturn(json_encode($return_arr));
            }

            if ($type == 2) {
                //print_r($GoodsCategory->create());exit;
                $GoodsCategory->save(); // 写入数据到数据库
                $GoodsLogic->refresh_cat($_POST['id']);
            } else {
                $insert_id = $GoodsCategory->add(); // 写入数据到数据库
                $GoodsLogic->refresh_cat($insert_id);
            }
            $return_arr = array(
                'status' => 1,
                'msg' => '操作成功',
                'data' => array('url' => U('Admin/Goods/categoryList')),
            );
            $this->ajaxReturn(json_encode($return_arr));
        }
    }

    public function goodsList()
    {
        $GoodsLogic = new GoodsLogic();
        // $brandList = $GoodsLogic->getSortBrands();
        $categoryList = $GoodsLogic->getSortCategory();
        $this->assign('categoryList', $categoryList);
        $this->assign('brandList', $brandList);

        $limitTime = M('active')->where(['type' => 'limit_goods'])->getField('end_time');
        $this->assign('limitTime', $limitTime);
        $this->assign('min_date', date('Y-m-d'));
        $this->display();

    }

    //显示抢购结束时间
    public function ajaxGoodsLimitTime()
    {
        $time = I('time');
        M('active')->where(['type' => 'limit_goods'])->setField('end_time', $time);
        $return_arr = array(
            'status' => 1,
            'msg' => '操作成功',
            'data' => '',
        );
        $this->ajaxReturn($return_arr);
    }


    public function addEditGoods()
    {

        $GoodsLogic = new GoodsLogic();
        $Goods = D('Goods'); //
        $type = $_POST['goods_id'] > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新
        $_POST['id'] = $goods_id = $_POST['goods_id'];
        //ajax提交验证
        if (($_GET['is_ajax'] == 1) && IS_POST) {

            //C('TOKEN_ON',false);
            if (!$Goods->create(NULL, $type))// 根据表单提交的POST数据创建数据对象
            {
                //  编辑
                $return_arr = array(
                    'status' => -1,
                    'msg' => '操作失败',
                    'data' => $Goods->getError(),
                );
                $this->ajaxReturn(json_encode($return_arr));
            } else {

                //  form表单提交
                // C('TOKEN_ON',true);
                $_POST['add_time'] = time(); // 上架时间
                $_POST['cat_id_2'] && ($_POST['cat_id'] = $_POST['cat_id_2']);
                $_POST['cat_id_3'] && ($_POST['cat_id'] = $_POST['cat_id_3']);

                //商品列表图
                $_POST['goods_img'] = $_POST['original_img'];

                //$_POST['extend_cat_id_2'] && ($Goods->extend_cat_id = $_POST['extend_cat_id_2']);
                //$_POST['extend_cat_id_3'] && ($Goods->extend_cat_id = $_POST['extend_cat_id_3']);

                if ($type == 2) {
                    $cre = $Goods->create();
                    //print_r($cre);exit;


                    $Goods->save(); // 写入数据到数据库
                    $Goods->afterSave($goods_id);
                } else {

                    $goods_id = $insert_id = $Goods->add(); // 写入数据到数据库
                    $Goods->afterSave($goods_id);
                }

                //$GoodsLogic->saveGoodsAttr($goods_id, $_POST['goods_type']); // 处理商品 属性

                $return_arr = array(
                    'status' => 1,
                    'msg' => '操作成功',
                    'data' => array('url' => U('Admin/Goods/goodsList')),
                );
                $this->ajaxReturn(json_encode($return_arr));
            }
        }

        $cat_list = M('goods_category')->where("parent_id = 0")->select();

        $goodsType = M("GoodsType")->select();
        $goodsInfo = M('Goods')->where('id=' . I('GET.id', 0))->find();
        $level_cat = $GoodsLogic->find_parent_cat($goodsInfo['cat_id']); // 获取分类默认选中的下拉框
        $goodsImages = M("GoodsImages")->where('goods_id =' . I('GET.id', 0))->select();
        $this->assign('goodsImages', $goodsImages);  // 商品相册
        $this->assign('goodsInfo', $goodsInfo);  // 商品详情
        // print_r($goodsInfo);
        $this->assign('cat_list', $cat_list);
        $this->assign('level_cat', $level_cat);
        // print_r($level_cat);
        $this->assign('goodsType', $goodsType);
        //print_r($goodsStyle);
        $this->initEditor(); // 编辑器
        $this->display('_goods');
    }

    /**
     * 删除商品
     */
    public function delGoods()
    {
        $goods_id = $_GET['id'];
        $error = '';

        // 判断此商品是否有订单
        $c1 = M('OrderGoods')->where("goods_id = $goods_id")->count('1');
        $c1 && $error .= '此商品有订单,不得删除! <br/>';

        // 商品团购
        //$c1 = M('group_buy')->where("goods_id = $goods_id")->count('1');
        //$c1 && $error .= '此商品有团购,不得删除! <br/>';

        // 商品退货记录
        //$c1 = M('return_goods')->where("goods_id = $goods_id")->count('1');
        //$c1 && $error .= '此商品有退货记录,不得删除! <br/>';

        if ($error) {
            $return_arr = array('status' => -1, 'msg' => $error, 'data' => '',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
            $this->ajaxReturn(json_encode($return_arr));
        }

        // 删除此商品
        M("Goods")->where('goods_id =' . $goods_id)->delete();  //商品表
        M("cart")->where('goods_id =' . $goods_id)->delete();  // 购物车
        //M("comment")->where('goods_id =' . $goods_id)->delete();  //商品评论
        //M("goods_consult")->where('goods_id =' . $goods_id)->delete();  //商品咨询
        M("goods_images")->where('goods_id =' . $goods_id)->delete();  //商品相册
        M("spec_goods_price")->where('goods_id =' . $goods_id)->delete();  //商品规格
        M("spec_image")->where('goods_id =' . $goods_id)->delete();  //商品规格图片
        //M("goods_attr")->where('goods_id =' . $goods_id)->delete();  //商品属性
        //M("goods_collect")->where('goods_id =' . $goods_id)->delete();  //商品收藏

        $return_arr = array('status' => 1, 'msg' => '操作成功', 'data' => '',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
        $this->ajaxReturn(json_encode($return_arr));
    }

    /**
     * 删除分类
     */
    public function delGoodsCategory()
    {
        // 判断子分类
        $GoodsCategory = M("GoodsCategory");
        $count = $GoodsCategory->where("parent_id = {$_GET['id']}")->count("id");
        $count > 0 && $this->error('该分类下还有分类不得删除!', U('Admin/Goods/categoryList'));
        // 判断是否存在商品
        $goods_count = M('Goods')->where("cat_id = {$_GET['id']}")->count('1');
        $goods_count > 0 && $this->error('该分类下有商品不得删除!', U('Admin/Goods/categoryList'));
        // 删除分类
        $GoodsCategory->where("id = {$_GET['id']}")->delete();
        $this->success("操作成功!!!", U('Admin/Goods/categoryList'));
    }

    /**
     * 初始化编辑器链接
     * 本编辑器参考 地址 http://fex.baidu.com/ueditor/
     */
    private function initEditor()
    {
        $this->assign("URL_upload", U('Admin/Ueditor/imageUp', array('savepath' => 'goods'))); // 图片上传目录
        $this->assign("URL_imageUp", U('Admin/Ueditor/imageUp', array('savepath' => 'article'))); //  不知道啥图片
        $this->assign("URL_fileUp", U('Admin/Ueditor/fileUp', array('savepath' => 'article'))); // 文件上传s
        $this->assign("URL_scrawlUp", U('Admin/Ueditor/scrawlUp', array('savepath' => 'article')));  //  图片流
        $this->assign("URL_getRemoteImage", U('Admin/Ueditor/getRemoteImage', array('savepath' => 'article'))); // 远程图片管理
        $this->assign("URL_imageManager", U('Admin/Ueditor/imageManager', array('savepath' => 'article'))); // 图片管理
        $this->assign("URL_getMovie", U('Admin/Ueditor/getMovie', array('savepath' => 'article'))); // 视频上传
        $this->assign("URL_Home", "");
    }

    public function ajaxGoodsList()
    {

        $gtype = I('gtype');
        $where = ' 1 = 1 '; // 搜索条件
        I('intro') && $where = "$where and " . I('intro') . " = 1";
        I('brand_id') && $where = "$where and brand_id = " . I('brand_id');
        (I('is_on_sale') !== '') && $where = "$where and is_on_sale = " . I('is_on_sale');
        $gtype && $where = $where . ' and gtype=' . $gtype;
        $cat_id = I('cat_id');
        // 关键词搜索
        $key_word = I('key_word') ? trim(I('key_word')) : '';
        if ($key_word) {
            $where = "$where and (goods_name like '%$key_word%' or goods_sn like '%$key_word%')";
        }

        if ($cat_id > 0) {
            $grandson_ids = getCatGrandson($cat_id);
            $where .= " and cat_id in(" . implode(',', $grandson_ids) . ") "; // 初始化搜索条件
        }

        $model = M('Goods');

        $count = $model->where($where)->count();
        $Page = new AjaxPage($count, 10);
        $show = $Page->show();
        $order_str = "";
        $goodsList = $model->where($where)->field('goods_id,goods_name,cat_id,goods_sn,goods_price,sort_order,store_count,is_recommend,is_hot,is_new,is_on_sale,gtype')->order($order_str)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        //print_r($goodsList);exit;
        $this->assign('goodsList', $goodsList);
        $this->assign('page', $show);// 赋值分页输出

        $catList = D('goods_category')->select();
        $catList = convert_arr_key($catList, 'id');
        $this->assign('catList', $catList);
        //print_r($catList);


        $this->display();
    }

    /**
     * 动态获取商品规格选择框 根据不同的数据返回不同的选择框
     */
    public function ajaxGetSpecSelect()
    {
        $goods_id = $_GET['goods_id'] ? $_GET['goods_id'] : 0;
        $GoodsLogic = new GoodsLogic();
        //$_GET['spec_type'] =  13;
        $specList = D('Spec')->where("type_id = " . $_GET['spec_type'])->order('`order` desc')->select();


        foreach ($specList as $k => $v)
            $specList[$k]['spec_item'] = D('SpecItem')->where("spec_id = " . $v['id'])->getField('id,item'); // 获取规格项

        $items_id = M('SpecGoodsPrice')->where('goods_id = ' . $goods_id)->getField("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id");
        $items_ids = explode('_', $items_id);

        // 获取商品规格图片
        if ($goods_id) {
            $specImageList = M('SpecImage')->where("goods_id = $goods_id")->getField('spec_image_id,src');
        }
        $this->assign('specImageList', $specImageList);

        $this->assign('items_ids', $items_ids);
        $this->assign('specList', $specList);
        $this->display('ajax_spec_select');
    }

    /**
     * 动态获取商品规格输入框 根据不同的数据返回不同的输入框
     */
    public function ajaxGetSpecInput()
    {
        $GoodsLogic = new GoodsLogic();
        $goods_id = $_REQUEST['goods_id'] ? $_REQUEST['goods_id'] : 0;
        $str = $GoodsLogic->getSpecInput($goods_id, $_POST['spec_arr']);
        exit($str);
    }


    /**
     * 商品类型  用于设置商品的属性
     */
    public function goodsTypeList()
    {
        $model = M("GoodsType");
        $count = $model->count();
        $Page = new Page($count, 100);
        $show = $Page->show();
        $goodsTypeList = $model->order("id desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('show', $show);
        $this->assign('goodsTypeList', $goodsTypeList);
        $this->display('goodsTypeList');
    }

    /**
     * 添加修改编辑  商品属性类型
     */
    public function addEditGoodsType()
    {
        $_GET['id'] = $_GET['id'] ? $_GET['id'] : 0;
        $model = M("GoodsType");
        if (IS_POST) {
            $model->create();
            if ($_GET['id'])
                $model->save();
            else
                $model->add();

            $this->success("操作成功!!!", U('Admin/Goods/goodsTypeList'));
            exit;
        }
        $goodsType = $model->find($_GET['id']);
        $this->assign('goodsType', $goodsType);
        $this->display('_goodsType');
    }

    /**
     * 商品属性列表
     */
    public function goodsAttributeList()
    {
        $goodsTypeList = M("GoodsType")->select();
        $this->assign('goodsTypeList', $goodsTypeList);
        $this->display();
    }

    /**
     *  商品属性列表
     */
    public function ajaxGoodsAttributeList()
    {
        //ob_start('ob_gzhandler'); // 页面压缩输出
        $where = ' 1 = 1 '; // 搜索条件
        I('type_id') && $where = "$where and type_id = " . I('type_id');
        // 关键词搜索
        $model = M('GoodsAttribute');
        $count = $model->where($where)->count();
        $Page = new AjaxPage($count, 13);
        $show = $Page->show();
        $goodsAttributeList = $model->where($where)->order('`order` desc,attr_id DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $goodsTypeList = M("GoodsType")->getField('id,name');
        $attr_input_type = array(0 => '手工录入', 1 => ' 从列表中选择', 2 => ' 多行文本框');
        $this->assign('attr_input_type', $attr_input_type);
        $this->assign('goodsTypeList', $goodsTypeList);
        $this->assign('goodsAttributeList', $goodsAttributeList);
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    /**
     * 添加修改编辑  商品属性
     */
    public function addEditGoodsAttribute()
    {

        $model = D("GoodsAttribute");
        $type = $_POST['attr_id'] > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新
        $_POST['attr_values'] = str_replace('_', '', $_POST['attr_values']); // 替换特殊字符
        $_POST['attr_values'] = str_replace('@', '', $_POST['attr_values']); // 替换特殊字符
        $_POST['attr_values'] = trim($_POST['attr_values']);

        if (($_GET['is_ajax'] == 1) && IS_POST)//ajax提交验证
        {
            C('TOKEN_ON', false);
            if (!$model->create(NULL, $type))// 根据表单提交的POST数据创建数据对象
            {
                //  编辑
                $return_arr = array(
                    'status' => -1,
                    'msg' => '',
                    'data' => $model->getError(),
                );
                $this->ajaxReturn(json_encode($return_arr));
            } else {
                // C('TOKEN_ON',true); //  form表单提交
                if ($type == 2) {
                    $model->save(); // 写入数据到数据库
                } else {
                    $insert_id = $model->add(); // 写入数据到数据库
                }
                $return_arr = array(
                    'status' => 1,
                    'msg' => '操作成功',
                    'data' => array('url' => U('Admin/Goods/goodsAttributeList')),
                );
                $this->ajaxReturn(json_encode($return_arr));
            }
        }
        // 点击过来编辑时
        $_GET['attr_id'] = $_GET['attr_id'] ? $_GET['attr_id'] : 0;
        $goodsTypeList = M("GoodsType")->select();
        $goodsAttribute = $model->find($_GET['attr_id']);
        $this->assign('goodsTypeList', $goodsTypeList);
        $this->assign('goodsAttribute', $goodsAttribute);
        $this->display('_goodsAttribute');
    }

    /**
     * 商品规格列表
     */
    public function specList()
    {
        $goodsTypeList = M("GoodsType")->select();
        $this->assign('goodsTypeList', $goodsTypeList);
        $this->display();
    }


    /**
     *  商品规格列表
     */
    public function ajaxSpecList()
    {
        //ob_start('ob_gzhandler'); // 页面压缩输出
        $where = ' 1 = 1 '; // 搜索条件
        I('type_id') && $where = "$where and type_id = " . I('type_id');
        // 关键词搜索
        $model = D('spec');
        $count = $model->where($where)->count();
        $Page = new AjaxPage($count, 13);
        $show = $Page->show();
        $specList = $model->where($where)->order('`type_id` desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $GoodsLogic = new GoodsLogic();
        foreach ($specList as $k => $v) {       // 获取规格项
            $arr = $GoodsLogic->getSpecItem($v['id']);
            $specList[$k]['spec_item'] = implode(' , ', $arr);
        }

        $this->assign('specList', $specList);
        $this->assign('page', $show);// 赋值分页输出
        $goodsTypeList = M("GoodsType")->select(); // 规格分类
        $goodsTypeList = convert_arr_key($goodsTypeList, 'id');
        $this->assign('goodsTypeList', $goodsTypeList);
        $this->display();
    }

    /**
     * 添加修改编辑  商品规格
     */
    public function addEditSpec()
    {

        $model = D("spec");
        $type = $_POST['id'] > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新
        if (($_GET['is_ajax'] == 1) && IS_POST)//ajax提交验证
        {
            C('TOKEN_ON', false);
            if (!$model->create(NULL, $type))// 根据表单提交的POST数据创建数据对象
            {
                //  编辑
                $return_arr = array(
                    'status' => -1,
                    'msg' => '',
                    'data' => $model->getError(),
                );
                $this->ajaxReturn(json_encode($return_arr));
            } else {
                // C('TOKEN_ON',true); //  form表单提交
                if ($type == 2) {
                    $model->save(); // 写入数据到数据库
                    $model->afterSave($_POST['id']);
                } else {
                    $insert_id = $model->add(); // 写入数据到数据库
                    $model->afterSave($insert_id);
                }
                $return_arr = array(
                    'status' => 1,
                    'msg' => '操作成功',
                    'data' => array('url' => U('Admin/Goods/specList')),
                );
                $this->ajaxReturn(json_encode($return_arr));
            }
        }
        // 点击过来编辑时
        $id = $_GET['id'] ? $_GET['id'] : 0;
        $spec = $model->find($id);
        $GoodsLogic = new GoodsLogic();
        $items = $GoodsLogic->getSpecItem($id);
        $spec[items] = implode(PHP_EOL, $items);
        $this->assign('spec', $spec);

        $goodsTypeList = M("GoodsType")->select();
        $this->assign('goodsTypeList', $goodsTypeList);
        $this->display('_spec');
    }

    /**
     * 更改指定表的指定字段
     */
    public function updateField()
    {
        $primary = array(
            'goods' => 'id',
            'goods_category' => 'id',
            'brand' => 'id',
            'goods_attribute' => 'attr_id',
            'ad' => 'ad_id',
        );
        $model = D($_POST['table']);
        $model->$primary[$_POST['table']] = $_POST['id'];
        $model->$_POST['field'] = $_POST['value'];
        $model->save();
        $return_arr = array(
            'status' => 1,
            'msg' => '操作成功',
            'data' => array('url' => U('Admin/Goods/goodsAttributeList')),
        );
        $this->ajaxReturn(json_encode($return_arr));
    }


}