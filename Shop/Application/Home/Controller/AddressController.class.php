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
     * 获取市
     */
    public function getCity()
    {
        $list = M('area_china')->where(array('type' => 3))->field('id as city_id,name')->select();
        $this->_returnData($list);
    }

    /**
     * 获取县/区
     */
    public function getDistrict()
    {
        $parameters = $this->_createParameters();
        //$city_id = $parameters['city_id'];
        $list = M('area_china')->where(array('type' => 4, 'parent_id' => $parameters['city_id']))->field('id as district_id,name')->select();
        $this->_returnData($list);
    }

}
