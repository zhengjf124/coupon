<?php
namespace Home\Controller;

/**
 * 会员接口
 * @author xiaoxu
 * @package Home\Controller
 */
class UserController extends MemberController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ##用户基本信息接口
     * URI : /ome/user/detail
     * @param :
     *     name   | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *
     * parameters:
     *     name   |  type  | null | description
     * -----------|--------|------|-------------
     *   passport | string | 必填 |  用户票据
     *
     * @return
     *    name   | type   | description
     * ----------|--------|----------------------
     * headimgurl| string | 用户头像
     * nick_name | string | 用户昵称
     *    sex    | string | 0-保密  1-男  2-女
     *  mobile   | string | 手机号码
     *
     */
    public function detail()
    {
        $sex = C('SEX');
        if (empty($this->user_info['headimgurl']) === false) {
            $data['headimgurl'] = SITE_URL . '/' . $this->user_info['headimgurl'];
        } else {
            $data['headimgurl'] = SITE_URL . '/';
        }
        $data['nick_name'] = $this->user_info['nick_name'];
        $data['sex'] = $sex[$this->user_info['sex']];
        $data['mobile'] = substr_replace($this->user_info['mobile'], '****', 3, 4);
        $this->_returnData($data);
    }

    /**
     * ##用户信息更新
     * URI : /Home/user/update
     * @param :
     *     name   | type   | null| description
     * -----------|--------|-----|-------------
     *  parameters| string | 必填 | 参数(json)
     *  headimgurl| string | 选填 | 头像
     *
     * parameters：
     * name      | type   | null| description
     * ----------|--------|-----|-------------
     *  passport | string | 必填 | 用户票据
     * nick_name | string | 选填 | 用户昵称
     *    sex    |  int   | 选填 | 用户性别
     *
     * name      | type   | null| description
     * ----------|--------|-----|-------------
     * headimgurl| string | 选填 | 头像
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     *   id    | int      | 用户ID
     *
     */
    public function update()
    {
        $parameters = $this->_createParameters();
        $data = [];

        if (empty($parameters['nick_name']) === false) {
            $data['nick_name'] = $parameters['nick_name'];
        }

        if (in_array($parameters['sex'], array(0, 1, 2))) {
            $data['nick_name'] = $parameters['sex'];
        }
        /*        if ($_FILES['headimgurl']) {
                    $upload = new \Think\Upload();// 实例化上传类
                    $upload->maxSize = $map['author'] = (1024 * 1024 * 3);// 设置附件上传大小 管理员10M  否则 3M
                    $upload->exts = array('jpg', 'gif', 'png', 'jpeg', 'bmp');// 设置附件上传类型
                    $upload->rootPath = 'Public/uploads/user/'; // 设置附件上传根目录
                    $upload->replace = true; // 存在同名文件是否是覆盖，默认为false
                    //$upload->saveName  =  'file_'.$id; // 存在同名文件是否是覆盖，默认为false

                    //$User->headimgurl = $parameters['nick_name'];

                }*/
        // var_dump($User);exit;
        if ($data == []) {
            $this->_returnError(123, '没有数据');
        }
        M('user')->where(['user_id' => $this->user_id])->data($data)->save();
        $this->_returnData();
    }

}//end
