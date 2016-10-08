<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 收藏商品接口
 * @author wubuze
 * @package Home\Controller
 */
class CollectController extends ApiController
{


    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();

    }

     /**
     * 我的收藏列表接口
     *
     * URI : /Home/Collect/index
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     * offset  | int   | 选填 |分页开始
     * limit  | int   | 选填 |每页数量
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * list    |  array   |  二维数组
     *  count   |  int    |  数组长度
     *
     * list :
     * name    | type     | description
     * --------|----------|-----------------
     * id      | int      | 收藏编号ID
     * goods_id| int      | 收藏商品id
     * goods_name| string | 商品名称
     * market_price | decimal | 市场价
     * goods_price | decimal   | 价格
     * goods_brief | string   | 商品简介,卖点
     * goods_img | string   | 商品图片url
     * is_new | int  | 是否是新品（1-是，0-否）
     * is_hot | int  | 是否是热卖 （同上）
     * is_best | int | 是否精品 （同上）
     * comment_count | int   | 评论数量
     */
     public function index()
    {
        $user  = $this->_checkPassport();//用户必须登入
        $user_id = $user['id'];
        $where = ['user_id' => $user_id];
        $offset = I('offset') ? I('offset') : 0;
        $limit = I('limit') ? I('limit')  : 20;
        $list = M('GoodsFavorite')->where($where)->field('id,goods_id')->limit($offset,$limit)->select();
        $count = M('GoodsFavorite')->where($where)->count();
        foreach($list  as & $val)
        {
            $goods = M('Goods')->field('goods_name,goods_price,market_price,goods_brief,is_hot,is_new,is_best,goods_img')
                ->where(['id'=>$val['goods_id']])->find();
            $val['goods_name'] = $goods['goods_name'];
            $val['goods_price'] = $goods['goods_price'];
            $val['market_price'] = $goods['market_price'];
            $val['goods_brief'] = $goods['goods_brief'];
            $val['is_hot'] = $goods['is_hot'];
            $val['is_new'] = $goods['is_new'];
            $val['is_best'] = $goods['is_best'];
            $val['goods_img'] = $goods['goods_img'];
            $val['comment_count'] = M('Comments')->where(['goods_id' => $val['goods_id']])->count();
        }
        $data = [
            'list'=>$list,
            'count'=>$count
        ];
        $this->_returnData($data);

       
    }

    /**
     * 加入收藏接口
     *
     * URI : /Home/Collect/add
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     * goods_id      | int   | 必填 |商品id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 收藏编号ID
     *
     */
    public function add()
    {
        $user  = $this->_checkPassport();//用户必须登入
        $user_id = $user['id'];
        $goods_id = I('goods_id');
        if(!$goods_id){
            $this->_returnError('50001','商品id不能为空');
        }
        $id = M('GoodsFavorite')->where(['user_id'=>$user_id, 'goods_id'=>$goods_id])->getField('id');
        if(!$id){
            $id = M('GoodsFavorite')->data(['user_id'=>$user_id, 'goods_id'=>$goods_id, 'add_time'=>time()])->add();

        }
        $data=[
            'id' =>$id
        ];
        $this->_returnData($data);


    }

    /**
     * 取消收藏接口
     *
     * URI : /Home/Collect/cancel
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport | string | 必填 |用户票据
     * goods_id        | int     | 必填 |收藏编号id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * goods_id | int      | 商品id
     *
     */
    public function cancel()
    {

        $user = $this->_checkPassport();//用户必须登入
        $goods_id = I('goods_id');
        $res = M('GoodsFavorite')->where(['goods_id'=>$goods_id,'user_id'=>$user['id']])->delete();
        if($res === false){
            $this->_returnError('50001','商品id错误');
        }
        $data=[
            'goods_id' =>$goods_id
        ];
        $this->_returnData($data);


    }

    /**
     * 判断商品是否已经收藏
     *
     * URI : /Home/Collect/isCollect
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport | string | 必填 |用户票据
     * goods_id  | int    | 必填 |收藏编号id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * status  | int      | 1-已收藏，0-未收藏
     *
     */
    public function isCollect()
    {

        $user = $this->_checkPassport();//用户必须登入
        $goods_id = I('goods_id');
        $res = M('GoodsFavorite')->where(['goods_id'=>$goods_id,'user_id'=>$user['id']])->find();
        $status = 0;
        if($res){
            $status = 1;
        }
        $data=[
            'status' =>$status
        ];
        $this->_returnData($data);


    }






   
}//end