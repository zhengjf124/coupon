<?php

namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 地址管理
 * Class AddressController
 * @package Home\Controller
 */
class AddressController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 获取城市 \n
     * URI : /home/address/getCity
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *   -------  | ------ | 不必填| ------
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     *   list   |  array  |  城市列表
     *
     * list :
     *    name   |  type  | description
     * ----------|--------|----------------------
     *   city_id |  int   |  城市ID
     * city_name | string |  城市名称
     *
     */
    public function getCity()
    {

        $list = M('area_china')->where(array('type' => 3))->field('id as city_id,name as city_name')->select();
        if (!is_array($list)) {
            $list = array();
        }
        $this->_returnData(['list' => $list]);
    }


    /**
     * 获取县/区 \n
     * URI : /home/address/getDistrict
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters:
     *   name   | type | null | description
     * ---------|------|------|-------------
     *  city_id |  int | 必填 | 城市ID
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     *   list   |  array  |  城市列表
     *
     * list :
     *      name     |  type  | description
     * --------------|--------|----------------------
     *  district_id  |  int   |  县/区ID
     * district_name | string |  县/区名称
     *    area_num   |  int   |  县/区下的商圈数量
     */
    public function getDistrict()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['city_id'])) {
            $this->_returnError(10020, '城市ID不合法');
        }
        $list = M('area_china')->where(array('type' => 4, 'parent_id' => $this->_parameters['city_id']))->field('id as district_id,name as district_name')->select();
        if (!is_array($list) || empty($list)) {
            $list = array();
        } else {
            foreach ($list as &$value) {
                $value['area_num'] = M('trading_area')->where(array('district_id' => $value['district_id']))->count();
            }
        }
        $this->_returnData(['list' => $list]);
    }

    /**
     * 获取获取商圈 \n
     * URI : /home/address/getTradingArea
     * @param :
     *     name   | type   | null | description
     * -----------|--------|------|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters:
     *     name     | type | null | description
     * -------------|------|------|-------------
     *  district_id |  int | 必填 | 县/区ID
     *
     * @return
     *    name  |  type   | description
     * ---------|---------|----------------------
     *   list   |  array  |  商圈列表
     *
     * list :
     *     name   |  type  | description
     * -----------|--------|----------------------
     *   area_id  |  int   |  商圈ID
     *  area_name | string |  商圈名称
     *
     */
    public function getTradingArea()
    {
        if (!preg_match('/^[1-9][0-9]*$/', $this->_parameters['district_id'])) {
            $this->_returnError(10021, '县区ID不合法');
        }
        $list = M('trading_area')->where(array('district_id' => $this->_parameters['district_id']))->field('area_id,area_name')->select();
        if (!is_array($list) || empty($list)) {
            $list = array();
        }
        $this->_returnData(['list' => $list]);
    }
}


