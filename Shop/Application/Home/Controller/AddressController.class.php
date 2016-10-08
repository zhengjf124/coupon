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
     *     city_id   |  int   |  县/区ID
     * district_name | string |  县/区名称
     *
     */
    public function getDistrict()
    {
        $parameters = $this->_createParameters();
        $list = M('area_china')->where(array('type' => 4, 'parent_id' => $parameters['city_id']))->field('id as district_id,name as district_name')->select();
        $this->_returnData(['list' => $list]);
    }

}