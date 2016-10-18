<?php

namespace Home\Controller;

require_once(APP_PATH . 'ApiController.class.php');

use Application\ApiController;

/**
 * 优惠券
 * Class CouponController
 * @package Home\Controller
 */
class CouponController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 获取优惠券详细信息 \n
     * URI : /home/coupon/detail
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters:
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  coupon_id |  int   | 必须  | 优惠券ID
     *  passport  | string | 可选  | 用户票据
     *
     * @return
     *      name    |  type  | description
     * -------------|--------|----------------------
     * coupon_detail|  array |  优惠券详情
     *  store_list  |  array |  门店列表
     *
     * coupon_detail:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   coupon_id  |  int   |  优惠券ID
     *  coupon_name | string |  优惠券名称
     *  coupon_desc | string |  描述
     * market_price | float  |  原价
     * coupon_price | float  |  实际价格
     * coupon_sales |  int   |  销量
     * coupon_banner| string |  详情页图片
     * coupon_detail| string |  详细介绍
     *   collect    |  int   |  是否收藏 0-未收藏、1-已收藏
     *
     * store_list:
     *      name    |  type  | description
     * -------------|--------|----------------------
     *   store_id   |  int   |  门店ID
     *  store_name  | string |  门店名称
     * comment_count|  int   |  评论次数
     *  store_phone | string |  联系电话
     *    address   | string |  地址
     * comment_level|  int   |  评论等级 0-5，0代表无评论，1-5分别代表1到5颗星
     *   distance   | string |  距离
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/coupon/detail/parameters/%7b%22passport%22%3a%22d2ab2b971ff0dc34b54c0eaa664873f0%22%2c%22coupon_id%22%3a%222%22%7d
     *
     */
    public function detail()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['coupon_id'])) {
            $this->_returnError(10044, '优惠券ID不合法');
        }

        $coupon_detail = M('coupons_sale')->where(array('coupon_id' => $this->_parameters['coupon_id'], 'is_delete' => 0))->field('coupon_id,coupon_name,coupon_desc,market_price,coupon_price,coupon_sales,coupon_banner,coupon_detail')->find();
        if (!$coupon_detail || !is_array($coupon_detail)) {
            $this->_returnError(10045, '优惠券不存在');
        }
        $coupon_detail['coupon_detail'] = htmlspecialchars_decode($coupon_detail['coupon_detail']);
        $coupon_detail['collect'] = 0;//未收藏

        if (preg_match('/^[0-9a-zA-Z]{32}$/', $this->_parameters['passport'])) {
            $user_id = M('user_passport')->getFieldByPassport($this->_parameters['passport'], 'user_id');
            if ($user_id) {
                $collect_info = M('coupons_favorite')->where(array('user_id' => $user_id, 'coupon_id' => $coupon_detail['coupon_id']))->field('collect_id')->find();
                if ($collect_info) {
                    $coupon_detail['collect'] = 1;//已收藏
                }
            }
        }

        $store_ids = M('coupons_store')->where(array('coupon_id' => $coupon_detail['coupon_id']))->getField('store_id', true);
        if ($store_ids) {
            $store_list = M('store')->where(array('store_id' => array('in', $store_ids), 'is_delete' => 0))->field('store_id,store_name,comment_count,store_phone,address')->order('sort_order')->select();
            if ($store_list) {
                foreach ($store_list as &$value) {
                    $value['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                    $value['distance'] = '<500m';//距离
                }
                unset($value);
            }
        }

        if (!isset($store_list)) {
            $store_list = array();
        }

        $this->_returnData(['coupon_detail' => $coupon_detail, 'store_list' => $store_list]);
    }

    public function test()
    {


        $data['a'] = ['z', 'j', 'f'];
        $data['b'] = ['z', 's', 'c'];
        $data['c'] = ['h', 'y', 'w'];
        $data['d'] = ['h', 'x'];
        $data['e'] = ['g', 'z', 'w'];
        $a = 'abcde abced abdce abdec abecd abedc acbde acbed acdbe acdeb acebd acedb adbce adbec adcbe adceb adebc adecb aebcd aebdc aecbd aecdb aedbc aedcb bacde baced badce badec baecd baedc bcade bcaed bcdae bcdea bcead bceda bdace bdaec bdcae bdcea bdeac bdeca beacd beadc becad becda bedac bedca cabde cabed cadbe cadeb caebd caedb cbade cbaed cbdae cbdea cbead cbeda cdabe cdaeb cdbae cdbea cdeab cdeba ceabd ceadb cebad cebda cedab cedba dabce dabec dacbe daceb daebc daecb dbace dbaec dbcae dbcea dbeac dbeca dcabe dcaeb dcbae dcbea dceab dceba deabc deacb debac debca decab decba eabcd eabdc eacbd eacdb eadbc eadcb ebacd ebadc ebcad ebcda ebdac ebdca ecabd ecadb ecbad ecbda ecdab ecdba edabc edacb edbac edbca edcab edcba ';
        // echo $a;
        $b = str_split($a, 6);
        foreach ($b as &$value) {
            $value = str_split(trim($value));
        }

        //echo json_encode($b);
        $haha = [];
        foreach ($b as $val) {
            foreach ($data[$val[0]] as $aaa) {
                foreach ($data[$val[1]] as $bbb) {
                    foreach ($data[$val[2]] as $ccc) {
                        foreach ($data[$val[3]] as $ddd) {
                            foreach ($data[$val[4]] as $eee) {
                                //echo '<a href="https://wanwang.aliyun.com/domain/searchresult/?keyword=' . $aaa . $bbb . $ccc . $ddd . $eee . '&suffix=.com"></a><br>';
                                echo $aaa . $bbb . $ccc . $ddd . $eee . '<br>';
                                //$haha[] = $aaa . $bbb . $ccc . $ddd . $eee;
                            }
                            unset($eee);
                        }
                        unset($ddd);
                    }
                    unset($ccc);
                }
                unset($bbb);
            }
            unset($aaa);
        }
        //echo json_encode($haha);
        /*        $str = 'abcde';
                $arr = str_split($str);
                $len = count($arr);
                $i = 0;
                $flag = array();
                $temp = array();
                while ($i < $len) {
                    $flag[$i] = 0;
                    $i++;
                }

                $this->quanpai($arr, $flag, 0, $len, $temp);*/


    }

    private function output($temp, $level)
    {
        for ($i = 0; $i < $level; $i++) {
            echo $temp[$i];
        }
        echo PHP_EOL;
    }

//产生全排列 递归参数传递一定要注意
    private function quanpai($arr, $flag, $level, $num, $temp)
    {
        if ($level >= $num) {
            $this->output($temp, $num);
            return;
        }
        for ($i = 0; $i < $num; $i++) {
            if ($flag[$i] == 0) {
                $temp[$level] = $arr[$i];
                $flag[$i] = 1;
                $this->quanpai($arr, $flag, $level + 1, $num, $temp);
                $flag[$i] = 0;
            }
        }
    }


}
