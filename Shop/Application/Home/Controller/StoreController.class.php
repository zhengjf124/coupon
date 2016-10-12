<?php

namespace Home\Controller;

require_once(APP_PATH . 'ApiController.class.php');

use Application\ApiController;

class StoreController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 获取商家列表 \n
     * URI : /home/store/storeLost
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *   city_id  |  int   | 必须  | 城市ID
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     *   list   |  array  |  城市列表
     *
     * list :
     *      name    |  type  | description
     * -------------|--------|----------------------
     *    store_id  |  int   |  商家ID
     *   store_name | string |  商家名称
     * comment_count| string |  评论次数
     *     label    | string |  标签
     *   keywords   | string |  关键字
     *   avg_price  | float  |  人均消费
     *   store_img  | string |  列表引导图
     * comment_level| string |  评论等级 0-5 0代表 无评论 1-5分别代表1到5颗星
     *   distance   | string |  距离
     *
     * @note
     * 测试地址：http://coupon.usrboot.com/home/store/storeLost/parameters/%7b%22city_id%22%3a%22350100%22%2c%22page%22%3a%221%22%7d
     */
    public function storeLost()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['city_id'])) {
            $this->_returnError(10020, '城市ID不合法');
        }

        $nowPage = $this->_parameters['page'];//当前页码
        if (!preg_match('/^[1-9][0-9]*$/', $nowPage)) {
            $this->_returnError(10040, '页码不合法');
        }

        if (preg_match('/^[1-9][0-9]*$/', $this->_parameters['area_id'])) {
            $where['area_id'] = $this->_parameters['area_id'];
        } else {
            $where['city_id'] = $this->_parameters['city_id'];
        }

        $where['is_show'] = 1;

        $listRows = 10;//一页的条数
        $totalRows = M('store')->where($where)->count();//总条数
        $totalPages = ceil($totalRows / $listRows);//总页数
        if ($nowPage > $totalPages) {
            $this->_returnError(10041, '页码超过了总页数');
        }

        $firstRow = $listRows * ($nowPage - 1);//从第几条开始查询

        $list = M('store')->where($where)->field('store_id,store_name,comment_count,label,keywords,avg_price,store_img')->order('sort_order')->limit($firstRow, $listRows)->select();
        $data['next_page'] = $nowPage + 1;
        $data['total_page'] = $totalPages;
        if (!is_array($list) || empty($list)) {
            $list = array();
        } else {
            foreach ($list as &$value) {
                $value['comment_level'] = 3;//评论等级0-5 0代表 无评论 1-5分别代表1到5颗星
                $value['distance'] = '<500m';//距离
            }
        }
        $data['list'] = $list;
        $this->_returnData($data);
    }

}