<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 会员接口
 * @author wubuze
 * @package Home\Controller
 */
class UserController extends ApiController
{

    public function __construct() {
        parent::__construct();
    }


     /**
     * ##用户基本信息接口
     * URI : /Home/user/detail
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 用户ID
     * headimgurl | string      | 用户头像
     * nick_name | string      | 用户昵称
     * sex | int   | 0-保密  1-男  2-女
     * birthday | string   | 生日
     *
     */
     public function detail()
    {

        $site_url = SITE_URL;
        $res  = $this->_checkPassport();//用户必须登入
        //$res['nick_name'] = 'wb';
        if($res['headimgurl']==''){
            $res['headimgurl'] = $site_url.'/Public/weixin/img-tx.jpg';
        }
        $data = [
            'id' => $res['id'],
            'nick_name' => $res['nick_name'],
            'headimgurl' => $res['headimgurl'],
            'sex' => $res['sex'],
            'birthday' => $res['birthday'],

        ];
        $this->_returnData($data);

       
    }

    /**
     * ##用户信息更新
     * URI : /Home/user/update
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     * nick_name  | string   | 选填 |用户昵称
     * sex        | int     | 选填   |用户性别
     * birthday  | string   | 选填 |生日
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      | int      | 用户ID
     *
     */
    public function update()
    {
        $res  = $this->_checkPassport();//用户必须登入
        $user_id = $res['id'];
        $up_data['nick_name']  = I('nick_name');
        $up_data['sex']       = I('sex');
        $up_data['birthday']  = I('birthday');

        M('User')->data($up_data)->where(['id'=>$user_id])->save();

        $data = [
            'id' => $res['id'],
        ];
        $this->_returnData($data);


    }

    /**
     * ##用户上传头像单独接口
     * URI : /Home/user/uploadHeadImg
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     * head_img_file| string  | 必填 |用户头像
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * imgUrl  | string   | 用户头像路径
     *
     */
    public function uploadHeadImg(){
        header("Access-Control-Allow-Headers: X-Requested-With");
        $res  = $this->_checkPassport();//用户必须登入
        $user_id = $res['id'];
        if($_FILES[head_img_file][tmp_name])
        {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =    $map['author'] = (1024*1024*3);// 设置附件上传大小 管理员10M  否则 3M
            $upload->exts      =    array('jpg', 'gif', 'png', 'jpeg','bmp');// 设置附件上传类型
            $upload->rootPath  =    './Public/upload/user/'; // 设置附件上传根目录
            $upload->replace   =    true; // 存在同名文件是否是覆盖，默认为false
            //$upload->saveName  =  'file_'.$id; // 存在同名文件是否是覆盖，默认为false
            // 上传文件
            $upinfo  =  $upload->upload();
            if(!$upinfo) {// 上传错误提示错误信息
                //$this->error($upload->getError());
                $this->_returnError('80003','文件格式错误a'.$upload->getError());
            }else{

                $comment_imgs = [];
                foreach($upinfo as $val){
                    $comment_img = '/Public/upload/user/'.$val['savepath'].$val['savename'];
                    $comment_imgs[] = $comment_img; // 上传的图片文件
                }
                $up_data['headimgurl'] = $comment_imgs[0]; // 上传的图片文件
                M('User')->data($up_data)->where(['id'=>$user_id])->save();
                //$this->_returnData(['imgUrl'=>$comment_imgs]);

                $this->_returnData(['imgUrl'=>$comment_imgs]);
                //$this->_returnError('80003','图片资源不存在');
                //$data['url']= 2;
                //echo json_encode($data);
            }

        }else{
            $this->_returnError('80003','图片资源不存在');
        }
    }


    /**
     * ##会员中心（我的收藏，我的积分，...）
     * URI : /Home/user/center
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * _passport  | string   | 必填 |用户票据
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * collect  | int     | 收藏数
     * credit  | int     | 积分
     * coupons  | int     | 优惠券数量
     * distribute  | int  |分销数量
     */

    public function center(){
        $res  = $this->_checkPassport();//用户必须登入
        $user_id = $res['id'];
        $data['collect'] = M('goods_favorite')->where(['user_id'=>$user_id])->count();
        $data['credit'] = M('Credit')->where(['user_id'=>$user_id])->getField('total_credit');
        if(!$data['credit']){
            $data['credit'] = 0;
        }
        $data['coupons'] = M('Coupons')->where(['user_id'=>$user_id,'status'=>0])->count();
        $data['distribute'] = 0;

        $this->_returnData($data);

    }










   
}//end