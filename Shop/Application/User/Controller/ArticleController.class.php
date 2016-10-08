<?php
namespace Home\Controller;
require_once(APP_PATH . 'ApiController.class.php');
use Application\ApiController;

/**
 * 文章中心
 * @author wubuze
 * @package Home\Controller
 */
class ArticleController extends ApiController
{


    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();

    }

     /**
     * 文章中心列表接口
     *
     * URI : /Home/Article/infoList
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * -------  | ------   | ----- |  ----
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * list    |  array   |  二维数组 list[next] 文章列表
     * count   |  int    |  数组长度
     *
     * list :
     * name    | type     | description
     * --------|----------|-----------------
     * id      | int      | id
     * title   | string   | 文章标题
     *
     */
     public function infoList()
    {

        //$where = ['cat_type' => 'help'];
        $where['show_in_nav'] = 1;
        $list = M('ArticleCat')->where($where)->field('id,cat_name')->order('id asc')->select();

        foreach($list as &$val)
        {
            $val['next'] = M('Article')->field('id,title')->where(['cat_id'=>$val['id'],'status'=>1])->select();
        }
        $data = [
            'list'=>$list,
            'count'=>count($list)
        ];
        $this->_returnData($data);

       
    }

    /**
     * 文章中心内容接口
     *
     * URI : /Home/Article/detail
     * @param :
     * name     | type     |  null |description
     * ---------|----------|-------|-------------
     * id       | int      | 必须   |  文章id
     *
     * @return
     *  name   | type     | description
     * --------|----------|----------------------
     * id      |  int    |  id
     * title   |  string |  标题
     * content |  string |  内容
     *
     */
    public function detail()
    {

        $where = ['id' => I('id')];

        $data = M('Article')->where($where)->field('id,title,content')->find();
        if(!$data['id']){
            $this->_returnError('20011','文章不存在');
        }

        $this->_returnData($data);


    }







   
}//end