<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 购物车接口
 * @author wubuze
 * @package Home\Controller
 */
class CartController extends ApiController
{

    /**
     *构造函数
     *
     */
    public function __construct() {
        parent::__construct();
        $this->cartLogic = new \Home\Logic\CartLogic();
    }
     /**
     * 购物车列表接口 \n
     * URI : /Home/Cart/index
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string   | 必填 |用户票据
     * offset   | int   | 选填 |分页开始 1-表示第一页，2-第二页 以此类推
     * limit    | int   | 选填 |每页数量
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     *  list    |  array  |  二维数组
     *  count   |  int    |  数组长度
     *  sum     |  int    |  购物车商品数量
     *
     * list :
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 购物车编号
     * goods_name|string  |商品名称
     * market_price|decimal |市场价
     * goods_price|decimal |商品本店价格
     * goods_num|int       |购买数量
     * goods_img|string |商品图片
     * spec_key_name|string | 商品规格 （内存:32G 颜色:金色）
     *
     */
     public function index()
    {
        $user  = $this->_checkPassport();//用户必须登入
        $user_id = $user['id'];

        $where = ['user_id' => $user_id];
        $offset = I('offset') ? I('offset') : 1;
        $offset = $offset-1;
        $limit = I('limit') ? I('limit')  : 20;
        $cart = M('Cart')->where($where)
            ->field('id,goods_id,goods_name,market_price,goods_price,goods_num,spec_key_name')
            ->limit($offset,$limit)->select();
        foreach($cart as &$val){
            $val['goods_img'] = M('Goods')->where(['id'=>$val['goods_id']])->getField('original_img');
            //商品库存
            if($val['spec_key_name']){
                $val['store_count'] = M('SpecGoodsPrice')->where(['goods_id'=>$val['goods_id'],'key_name'=>$val['spec_key_name']])->getField('store_count');
            }else{
                $val['store_count'] = M('Goods')->where(['id'=>$val['goods_id']])->getField('store_count');
            }
        }
        $count = M('Cart')->where($where)->count();
        $sum = $this->cartLogic->cart_goods_num($user_id);
        $data = ['list' => $cart, 'count' => $count , 'sum'=>$sum];
        $this->_returnData($data);

       
    }

    /**
     * 加入购物车(或立即购买)接口 \n
     * URI : /Home/Cart/add
     *
     * @param :
     *
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string | 必填 |用户票据
     * goods_id   | int    | 必填 |商品id
     * goods_num  | int    | 必填 |商品数量
     * goods_spec | array  | 必填 |商品规格数组
     * buy_type   | string | 必填 |buying(立即购买); cart(加入购物车)（默认是加入购物车）;
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     *  count  | int      | 购物车商品数
     *  cart_id| int      | 购物车id(立即购买需要)
     *
     * ###goods_spec (规格数据举例如下:)
     *
     * ###产品规格：
     * - 网络：3G，4G；
     * - 屏幕：   触屏；
     * - 颜色：   白色，金色；
     *
     * ###选中的是：
     * - 3G, 触屏, 白色
     *
     * ### HTML标签可以这样填写
     * - <input type="radio" style="display:none;" name="goods_spec[网络]" value="3G的id" checked="checked">
     * - <input type="radio" style="display:none;" name="goods_spec[屏幕]" value="触屏的id" checked="checked">
     * - <input type="radio" style="display:none;" name="goods_spec[颜色]" value="白色的id" checked="checked">
     *
     * ### JSON 格式应该是这样：
     * - 'goods_spec':{'网络':2,'屏幕':3,'颜色':5}
     *
     * @note
     *
     *
     */
    public function add()
    {
        $user  = $this->_checkPassport();//用户必须登入
        $user_id = $user['id'];

        $goods_id = I("goods_id")?I("goods_id"):0; // 商品id
        $goods_num = I("goods_num");// 商品数量
        $goods_spec = I("goods_spec"); // 商品规格
        $buy_type = I("buy_type",'cart'); // 购买方式

        $result = $this->cartLogic->addCart($goods_id, $goods_num, $goods_spec ,$user_id, $buy_type); // 将商品加入购物车

        if($result['status']>0){
            $this->_returnError($result['status'],$result['msg']);
        }else{
            $data = ['count'=>$result['result'],'cart_id'=>$result['cart_id']];
            $this->_returnData($data);
        }


    }



    /**
     * 删除购物车一个商品接口 \n
     * URI : /Home/Cart/delete
     * @param :
     *
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     * id      | int   | 必填 |购物车编号id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 购物车编号
     *
     */
    public function delete()
    {
        $user = $this->_checkPassport();//用户必须登入
        $user_id = $user['id'];
        $id = I('id');
        $res = M('Cart')->data(['id'=>$id,'user_id'=>$user_id])->delete();
        if($res==false){
            $this->_returnError('50012','商品数据格式有误');
        }
        $data = [
            'id' => $id
        ];
        $this->_returnData($data);
    }

    /**
     * 编辑购物车商品接口 \n
     * URI : /Home/Cart/editGoods
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     * goods      | array    | 必填 |购物车商品数组 [{"cart_id":1,"goods_num:"1},{"cart_id":2,"goods_num":1}]
     * goods:
     * name     | type     | null |description
     * ---------|----------|------|------------
     * cart_id | int      | 必填  | 购物车id
     * goods_num| int      | 必填  | 商品数量
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     *  count  | int      | 更新的商品条数
     *
     */
    public function editGoods()
    {
        $user = $this->_checkPassport();//用户必须登入
        $user_id = $user['id'];
        $goods = I('goods');
        $this->_returnError('50012','商品数据格式有误'.json_encode($goods));

        if(!is_array($goods)){
            $this->_returnError('50012','商品数据格式有误');
        }
        //$this->_returnError('50012','商品数据格式有误'.json_encode($goods));
        foreach($goods as $val){

            M('Cart')->where(['id'=>$val['cart_id'],'user_id'=>$user_id])->data(['goods_num'=>$val['goods_num']])->save();

        }

        $count = count($goods);
        $data = [
            'count' => $count
        ];

        $this->_returnData($data);
    }



}//end