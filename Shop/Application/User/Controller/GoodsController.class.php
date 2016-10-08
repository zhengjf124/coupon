<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;
use Home\Logic\PageLogic;
use Home\Logic\DemoLogic;

/**
 * 商品接口
 * @author wubuze
 * @package Home\Controller
 */
class GoodsController extends ApiController
{

    /**
     * 我是构造函数
     *
     */
    public function __construct() {
        parent::__construct();

    }
    /**
    * 商品(或商品搜索)列表接口 \n
    * URI : /Home/goods/index
    * @param :
    *
    * name     | type     |  null |description
    * ---------|----------|-------|-------------
    * order_by | string   | 选填   |综合排序 order_sort-desc \n 销量最高 order_sales-desc \n 销量最低 order_sales-asc \n 价格最高 order_price-desc \n 价格最低 order_price-asc
    *  offset  |   int    | 选填   |分页开始
    *  limit   |   int    | 选填   |每页几条
    *  cat_id  |   int    | 选填   |分类id
    *  keyword |   string | 选填   |搜索关键字
    *
    * @return
    * name   | type     | description
    * --------|----------|----------------------
    * list     | array   | 商品列表二维数组
    * count    | int     | 商品总数
     *
    * list :
    * name   | type     | description
    * -------|----------|----------------------
    * id     | int      | 商品id
    * goods_name | string  | 商品名称
    * market_price | decimal | 市场价
    * goods_price | decimal   | 价格
    * goods_brief | string   | 商品简介,卖点
    * goods_img | string   | 商品图片url
    * is_new | int  | 是否是新品（1-是，0-否）
    * is_hot | int  | 是否是热卖 （同上）
    * is_best | int | 是否精品 （同上）
    * comment_count | int   | 评论数量
    *
    */
    public function index(){
        $cat_id = I('cat_id',0);
        $where = [];
        $where['is_on_sale'] = 1;
        $where['gtype'] = 0;//0-普通商品，1-团购商品，2-限时抢购
        $order = [];
        $offset = I('offset') ? I('offset') : 0;
        $limit = I('limit') ? I('limit')  : 20;
        $keyword = I('keyword') ? I('keyword')  : '';
        if(intval($cat_id)>0){
            $where['cat_id'] = $cat_id;
        }

        if($keyword){
            $where['goods_name'] = array('like','%'.$keyword.'%');
        }
        if(I('order_by')){
            $order_by = explode('-',I('order_by'));
            if($order_by[0]=='order_sort'){
                $order = 'sort_order desc';
            }elseif($order_by[0]=='order_sales'){
                $order = 'goods_sales '.$order_by[1];

            }elseif($order_by[0]=='order_price'){
                $order = 'goods_price '.$order_by[1];
            }else{
                $order = 'sort_order desc';
            }
        }

        $count = M('Goods')->where($where)->count();
        $goods = M('Goods')->field('id,goods_name,goods_price,market_price,goods_brief,is_hot,is_new,is_best,goods_img')
            ->limit($offset, $limit)
            ->where($where)->order($order)->select();
        foreach($goods as &$g){
            $g['comment_count'] = M('Comments')->where(['goods_id' => $g['id']])->count();
        }

        $data = ['list'=>$goods, 'count'=>$count];
        $this->_returnData($data);

    }




    /**
     * 商品详细页面接口 \n
     * URI : /Home/Goods/detail
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * id       | int      | 必填   |商品id  （id为空错误码 error_code = 50001）
     *
     * @return
     * name   | type     | description
     * -------|----------|----------------------
     * goods  | array    | 商品信息
     * goods_images| array | 商品相册二维数组
     * goods_comment|int   | 商品评价数量
     * specList     |array|商品规格列表 [查看详情](http://demo.gnwai.com/wbz/images/specList.jpg)
     * spec_goods_price|array|商品规格对应价格列表 [查看详情](http://demo.gnwai.com/wbz/images/spec_goods_price.jpg)
     *
     * goods :
     * name   | type     | description
     * -------|----------|----------------------
     * id     | int      | 商品id
     * goods_name | string      | 商品名称
     * market_price | decimal   | 市场价
     * goods_price | decimal   | 商品价格
     * goods_brief | string   | 商品简介
     * store_count | string   | 商品库存
     * is_best     | int   | 促销 （是否精品 1-是 0-不是）
     * is_hot      | int   | 促销 （是否热卖 1-是 0-不是）
     * is_new      | int   | 促销 （是否新品 1-是 0-不是）
     * limit_time  | string | 限时抢购结束时间
     * gtype       | int    | 0-普通商品，2-限时抢购商品
     *
     * goods_images
     * name   | type     | description
     * -------|----------|----------------------
     * image_url   | string      | 商品图片url
     *
     *
     */
    public function detail(){
        $id = I("id");
        if(!$id){
            $this->_returnError('50001','商品id不能为空');
        }
        $goodsLogic = new \Home\Logic\GoodsLogic();
        $goods = M('Goods')->field('id,cat_id,goods_name,goods_price,market_price,store_count,goods_brief,is_hot,is_best,is_new,spec_type,gtype,end_time')
            ->where(['id' => $id] )->find();
        if($goods['gtype']==2)
        {//限时抢购
            $limit_time = M('active')->where(['type'=>'limit_goods'])->getField('end_time');
            if(!$limit_time) $limit_time = '';
            $goods['limit_time'] = $limit_time;
        }
        $goods_images = M('GoodsImages')->where("goods_id=".$id)->select();
        //商品规格

        $specList = $goodsLogic->get_spec($goods['id']);
        //print_r($specList);
        $spec_goods_price  = M('spec_goods_price')->where("goods_id = ".$goods['id'])->getField("key,price,store_count"); // 规格 对应 价格 库存表

        $goods_comment = 0;
        $data['goods'] = $goods;
        $data['goods_images'] = $goods_images;
        $data['goods_comment'] = $goods_comment;
        $data['specList'] = $specList;
        $data['spec_goods_price'] = $spec_goods_price;
        $this->_returnData($data);

    }

    /**
     * 商品详情猜你喜欢商品列表
     * URI : /Home/Goods/youLike
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string   | 选填   | 用户票据没有的时候可以不需要
     *
     * @return
     * name   | type     | description
     * -------|----------|----------------------
     * goods_list|array  | 商品列表
     *
     * goods_list
     * name   | type     | description
     * -------|----------|----------------------
     * id      | int | 商品id
     * goods_name |string|商品名称
     * goods_price|decimal|商品价格
     * goods_img|string|商品图片
     *
     */

    public function youLike(){
        $passport = I('_passport');
        $where['goods_type'] = 0;//0-普通商品，1-团购商品，2-限时抢购

        if($passport){
            $goods = M('Goods')->where($where)->field('id,goods_name,goods_price,goods_img')->limit(4)->select();
        }else{
            $goods = M('Goods')->where($where)->field('id,goods_name,goods_price,goods_img')->limit(4)->select();
        }

        $data['goods_list'] = $goods;
        $this->_returnData($data);
    }

    /**
     * 商品内容详情 \n
     * URI : /Home/Goods/content
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * id       | int      | 必填   |商品id
     *
     * @return
     * name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 商品id
     * goods_desc | html   | 商品详细内容
     *
     */
    public function content(){
        $id = I("id");
        if(!$id){
            $this->_returnError('50001','商品id不能为空');
        }
        $goods = M('Goods')->field('id,goods_desc')->where(['id' => $id] )->find();
        $data = $goods;
        $this->_returnData($data);


    }



    /**
     * ###限时抢购商品列表
     * URI : /Home/goods/buyLimit
     * @param :
     *
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     *  offset  |   int    | 选填   |分页开始
     *  limit   |   int    | 选填   |每页几条
     *
     * @return
     * name   | type     | description
     * --------|----------|----------------------
     * list     | array   | 商品列表二维数组
     * count    | int     | 商品总数
     * limit_time| string   | 结束时间
     *
     * list :
     * name   | type     | description
     * -------|----------|----------------------
     * id     | int      | 商品id
     * goods_name | string  | 商品名称
     * market_price | decimal | 市场价
     * goods_price | decimal   | 价格
     * goods_img | string   | 商品图片url
     */
    public function buyLimit(){

        $where = [];
        $where['is_on_sale'] = 1;
        $where['goods_type'] = 2;//0-普通商品，1-团购商品，2-限时抢购
        $offset = I('offset') ? I('offset') : 0;
        $limit = I('limit') ? I('limit')  : 20;
        $where['limit_time'] = array('gt',time());
        $order = 'id desc';

        $count = M('Goods')->where($where)->count();
        $goods = M('Goods')->field('id,goods_name,goods_price,market_price,goods_img')
            ->limit($offset, $limit)
            ->where($where)->order($order)->select();
        $endtime = M('active')->where(['type'=>'limit_goods'])->getField('end_time');
        $endtime = date('Y/m/d H:i:s',$endtime);
        $data = ['list'=>$goods, 'count'=>$count,'limit_time'=>$endtime];
        $this->_returnData($data);

    }

    /**
     * ###团购商品列表
     * URI : /Home/goods/buyGroup
     * @param :
     *
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     *  offset  |   int    | 选填   |分页开始
     *  limit   |   int    | 选填   |每页几条
     *
     * @return
     * name   | type     | description
     * --------|----------|----------------------
     * list     | array   | 商品列表二维数组
     * count    | int     | 商品总数
     *
     * list :
     * name   | type     | description
     * -------|----------|----------------------
     * id     | int      | 商品id
     * goods_name | string  | 商品名称
     * market_price | decimal | 市场价
     * goods_price | decimal   | 价格
     * goods_img | string   | 商品图片url
     * end_time | string   | 团购结束时间
     */
    public function buyGroup(){

        $where = [];
        $where['is_on_sale'] = 1;
        $where['goods_type'] = 1;//0-普通商品，1-团购商品，2-限时抢购
        $offset = I('offset') ? I('offset') : 0;
        $limit = I('limit') ? I('limit')  : 20;
        $where['limit_time'] = array('gt',time());
        $order = 'id desc';

        $count = M('Goods')->where($where)->count();
        $goods = M('Goods')->field('id,goods_name,goods_price,market_price,goods_img,end_time')
            ->limit($offset, $limit)
            ->where($where)->order($order)->select();
        foreach($goods as &$val){
            $val['end_time'] = date('Y/m/d H:i:s',$val['end_time']);
        }
        $data = ['list'=>$goods, 'count'=>$count];
        $this->_returnData($data);

    }










}//end
