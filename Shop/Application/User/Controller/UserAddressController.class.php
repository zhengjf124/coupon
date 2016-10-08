<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 收货地址
 * @author fangdexin
 * @package Home\Controller
 */
class UserAddressController extends ApiController
{
   

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        
        $this->addressLogic = new \Home\Logic\AddressLogic();
    }
     /**
     * ##用户收货地址列表接口##
     * URI : /Home/userAddress/index
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     *  user_address      | array      | 二维数组
     *
     * user_address :
     * name   | type     | description
     * --------|----------|----------------------
     * id      | int      | ID
     * consignee| string  | 收货人
     * province | string   | 省
     * city     | string   | 市
     * district | string   | 区
     * address  | string   | 详细地址
     * mobile   | string   | 手机号
     * is_default  | int   | 是否是默认地址 1-默认  0-非默认
     *
     */
    
    public function index()
    {
        $user  = $this->_checkPassport();
        $user_id = $user['id'];
        //$user_address = M('UserAddress')->field('id,consignee,province,city,address,district,mobile,is_default')->where(['user_id'=>$user_id])->select();
        $user_address = $this->addressLogic->getList($user_id);
        $data = ['user_address' => $user_address];
        $this->_returnData($data);
    }


    /**
     * ##获取一条收货地址接口##
     * URI : /Home/UserAddress/detail
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     *  id        | int      |  必填  | 收货地址id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int     | 收货地址id
     * consignee | string | 收货人
     * province | string  | 省
     * city     | string   | 市
     * district | string   | 区
     * address | string   | 详细地址
     * mobile | string   | 手机号
     *
     */
    public function detail()
    {
        $user  = $this->_checkPassport();
        $id = I('id');
        if(!$id){
            $this->_returnError('70001','收货地址id有误');
        }
        //$data = M('UserAddress')->field('id,consignee,province,city,district,address,mobile')->where(['id'=>$id])->find();
        $addressLogic = new \Home\Logic\AddressLogic();
        $data = $addressLogic->getAddress($id);
        $this->_returnData($data);


    }



    /**
     * ##添加一条收货地址接口## 
     * URI : /Home/UserAddress/add
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string | 必填 |用户票据
     * consignee| string | 必填 | 收货人
     * province | string | 必填 | 省
     * city     | string | 必填 | 市
     * district | string | 必填   | 区
     * address  | string | 必填   | 详细地址
     * mobile | string   | 必填   | 手机号
     * post   | string   |  选填   | 邮编
     * tel    | string   |  选填   | 收货人电话
     * email  | string   |  选填   | 收货人邮箱
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 收货地址id
     *
     */
    public function add()
    {
        if(IS_POST)
        {
            $user  = $this->_checkPassport();
            $userId     = $user['id'];
            $consignee   = I('consignee');
            $province    = I('province');
            $city        = I('city');
            $district    = I('district');
            $address     = I('address');
            $mobile      = I('mobile');
            $post        = I('post');
            $tel         = I('tel');
            $email       = I('email');
            
            if( empty($consignee) || empty($province) || empty($city) || empty($district) || empty($address) || empty($mobile) )
            {

                $this->_returnError('70002' , '数据不完整');

            }


            $record = [
                "user_id"    => $userId,
                "consignee"  => $consignee,
                "province"   => $province,
                "city"       => $city,
                "district"   => $district,
                "address"    => $address,
                "mobile"     => $mobile,
                "post"       => $post,
                "tel"        => $tel,
                "email"      => $email
            ];
            
            $res = M("UserAddress")->where(['is_default'=>1,'user_id'=>$userId])->find();
            if(!$res){
                $record['is_default'] = 1;
            }
            $id = M("UserAddress")->add($record);

            $data = ['id'=>$id];
            
            $this->_returnData($data);
        }else{
            $this->_returnError('1','post');
        }
        
    }

    /**
     * ##修改收货地址##
     * URI : /Home/UserAddress/update
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string | 必填 |用户票据
     * consignee| string | 必填 | 收货人
     * province | string | 必填 | 省
     * city     | string | 必填 | 市
     * district | string | 必填   | 区
     * address  | string | 必填   | 详细地址
     * mobile | string   | 必填   | 手机号
     * post   | string   |  选填   | 邮编
     * tel    | string   |  选填   | 收货人电话
     * email  | string   |  选填   | 收货人邮箱
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * status  | int      | 操作状态(成功 1，失败0)
     *
     */
    public function update(){
        if(IS_POST)
        {
            $user  = $this->_checkPassport();
            $id          = I('id');
            if(!$id)
            {
                $this->_returnError('70001','收货地址id有误');
            }
            
            
            $consignee   = I('consignee');
            $province    = I('province');
            $city        = I('city');
            $district    = I('district');
            $address     = I('address');
            $mobile      = I('mobile');
            $post        = I('post');
            $tel         = I('tel');
            $email       = I('email');
            
            if( empty($consignee) || empty($province) || empty($city) || empty($district) || empty($address) || empty($mobile) )
            {

                $this->_returnError('70002' , '数据不完整');

            }


            $record = [
                "id"         => $id,
                "consignee"  => $consignee,
                "province"   => $province,
                "city"       => $city,
                "district"   => $district,
                "address"    => $address,
                "mobile"     => $mobile,
                "post"       => $post,
                "tel"        => $tel,
                "email"      => $email
            ];

            $res = M("UserAddress")->save($record);

            $data = ['status'=>$res];

            $this->_returnData($data);
        }else{
            $this->_returnError('1','post');
        }
        
    }


    /**
     * ##删除收货地址## 
     * URI : /Home/UserAddress/delete
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport| string | 必填 |用户票据
     * id       | int    | 必填 |收货地址id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * status  | int      | 操作状态(成功 1，失败0)
     *
     */
    public function delete()
    {
        $user  = $this->_checkPassport();
        
        $id = I('id');
        if(!$id){
            $this->_returnError('70001','收货地址id有误');
        }
        
        $res = M("UserAddress")->where(["id"=>$id])->delete();
        
        if(!$res)
        {
            $this->_returnError('70003' , '操作失败');
        }
        
        $data = ["status"=>$res];
        
        $this->_returnData($data);
        
    }



   
}//end