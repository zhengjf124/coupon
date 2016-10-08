<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: 当燃      
 * Date: 2015-09-09
 */
namespace Admin\Controller;
use Admin\Logic\ArticleCatLogic;

class ArticleController extends BaseController {

    public function categoryList(){
        $ArticleCat = new ArticleCatLogic(); 
        $cat_list = $ArticleCat->art_cat_list(0, 0, false);
        //print_r($cat_list);
        $this->assign('cat_list',$cat_list);
        $this->display('categoryList');
    }
    
    public function category(){
        $ArticleCat = new ArticleCatLogic();  
 		$act = I('GET.act','add');
        $this->assign('act',$act);
        $cat_id = I('GET.cat_id');
        $cat_info = array();
        if($cat_id){
            $cat_info = D('article_cat')->where('id='.$cat_id)->find();
            $this->assign('cat_info',$cat_info);
        }
        //print_r($cat_info);
        $cats = $ArticleCat->art_cat_list(0,$cat_info['parent_id'],true);
        //print_r($cats);
        $this->assign('cat_select',$cats);
        $this->display();
    }
    
    public function articleList(){
        $Article =  M('Article'); 
        $res = $list = array();
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 20 : $_REQUEST['size'];
        
        $where = " 1 = 1 ";
        $keywords = trim(I('keywords'));
        $keywords && $where.=" and title like '%$keywords%' ";
        $res = $Article->field('id,title,cat_id,add_time,status')->where($where)->order('id desc')->page("$p,$size")->select();
        $count = $Article->where($where)->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出

        $ArticleCat = new ArticleCatLogic();
        $cats = $ArticleCat->art_cat_list(0,0,false);
        if($res){
        	foreach ($res as $val){
        		$val['category'] = $cats[$val['cat_id']]['cat_name'];
        		$val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);        		
        		$list[] = $val;
        	}
        }
        $this->assign('list',$list);// 赋值数据集
        //print_r($list);
        $this->assign('page',$page);// 赋值分页输出
        $this->display('articleList');
    }
    
    public function article(){
        $ArticleCat = new ArticleCatLogic();
 		$act = I('GET.act','add');
        $info = array();
        $info['publish_time'] = time()+3600*24;
        if(I('GET.article_id')){
           $article_id = I('GET.article_id');
           $info = D('article')->where('id='.$article_id)->find();

        }

        $cats = $ArticleCat->art_cat_list(0,$info['cat_id'],true);
        $this->assign('cat_select',$cats);
        //print_r($cats);
        $this->assign('act',$act);
        $this->assign('info',$info);
        $this->initEditor();
        $this->display();
    }
    
    
    /**
     * 初始化编辑器链接
     * @param $post_id post_id
     */
    private function initEditor()
    {
        $this->assign("URL_upload", U('Admin/Ueditor/imageUp',array('savepath'=>'article')));
        $this->assign("URL_fileUp", U('Admin/Ueditor/fileUp',array('savepath'=>'article')));
        $this->assign("URL_scrawlUp", U('Admin/Ueditor/scrawlUp',array('savepath'=>'article')));
        $this->assign("URL_getRemoteImage", U('Admin/Ueditor/getRemoteImage',array('savepath'=>'article')));
        $this->assign("URL_imageManager", U('Admin/Ueditor/imageManager',array('savepath'=>'article')));
        $this->assign("URL_imageUp", U('Admin/Ueditor/imageUp',array('savepath'=>'article')));
        $this->assign("URL_getMovie", U('Admin/Ueditor/getMovie',array('savepath'=>'article')));
        $this->assign("URL_Home", "");
    }
    
    
    public function categoryHandle(){
    	$data = I('post.');
        //print_r($data);exit;
        if($data['act'] == 'add'){
            if($data['parent_id']>0){
                $parent_level = M('article_cat')->where(['id'=>$data['parent_id']])->getField('level');
                $data['level'] = $parent_level+1;
            }else{
                $data['level'] = 1;
            }
            //print_r($data);exit;
            $d = D('article_cat')->add($data);
        }
        
        if($data['act'] == 'edit')
        {
        	if ($data['cat_id'] == $data['parent_id'])
			{
        		$this->error("所选分类的上级分类不能是当前分类",U('Admin/Article/category',array('cat_id'=>$data['cat_id'])));
        	}
//        	$ArticleCat = new ArticleCatLogic();
//        	$children = array_keys($ArticleCat->article_cat_list($data['cat_id'], 0, false)); // 获得当前分类的所有下级分类
//        	if (in_array($data['parent_id'], $children))
//        	{
//        		$this->error("所选分类的上级分类不能是当前分类的子分类",U('Admin/Article/category',array('cat_id'=>$data['cat_id'])));
//        	}
        	$d = D('article_cat')->where("id=$data[cat_id]")->save($data);
        }
        
        if($data['act'] == 'del'){      	
        	$res = D('article_cat')->where('parent_id ='.$data['cat_id'])->select(); 
        	if ($res)
        	{
        		exit(json_encode('还有子分类，不能删除'));
        	}
        	$res = D('article')->where('id ='.$data['cat_id'])->select();
        	if ($res)
        	{
        		exit(json_encode('非空的分类不允许删除'));
        	}      	
        	$r = D('article_cat')->where('id='.$data['cat_id'])->delete();
        	if($r) exit(json_encode(1));
        }
        if($d){
        	$this->success("操作成功",U('Admin/Article/categoryList'));
        }else{
        	$this->error("操作失败",U('Admin/Article/categoryList'));
        }
    }
    
    public function aticleHandle(){
        $data = I('post.');
        $data['publish_time'] = strtotime($data['publish_time']);

        //$data['content'] = htmlspecialchars(stripslashes($_POST['content']));
        if($data['act'] == 'add'){
                $data['click'] = mt_rand(1000,1300);
        	$data['add_time'] = time(); 
            $r = D('article')->add($data);
        }
        
        if($data['act'] == 'edit'){
            //print_r($data);exit;
            $r = D('article')->where('id='.$data['article_id'])->save($data);
        }
        
        if($data['act'] == 'del'){
        	$r = D('article')->where('id='.$data['article_id'])->delete();
        	if($r) exit(json_encode(1));       	
        }
       // $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Admin/Article/articleList');
        if($r){
            $this->success("操作成功",U('Admin/Article/articleList'));
        }else{
            $this->error("操作失败");
        }
    }
    
    
    public function link(){
    	$act = I('GET.act','add');
    	$this->assign('act',$act);
    	$link_id = I('GET.link_id');
    	$link_info = array();
    	if($link_id){
    		$link_info = D('friend_link')->where('link_id='.$link_id)->find();
    		$this->assign('info',$link_info);
    	}
    	$this->display();
    }
    
    public function linkList(){
    	$Ad =  M('friend_link');
    	$res = $Ad->where('1=1')->order('orderby')->page($_GET['p'].',10')->select();
    	if($res){
    		foreach ($res as $val){
    			$val['target'] = $val['target']>0 ? '开启' : '关闭';
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);// 赋值数据集
    	$count = $Ad->where('1=1')->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
    	$show = $Page->show();// 分页显示输出
    	$this->assign('page',$show);// 赋值分页输出
    	$this->display();
    }
    
    public function linkHandle(){
        $data = I('post.');
    	if($data['act'] == 'add'){
    		stream_context_set_default(array('http'=>array('timeout' =>2)));send_http_status('311');
    		$r = D('friend_link')->add($data);
    	}
    	if($data['act'] == 'edit'){
    		$r = D('friend_link')->where('link_id='.$data['link_id'])->save($data);
    	}
    	
    	if($data['act'] == 'del'){
    		$r = D('friend_link')->where('link_id='.$data['link_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	
    	if($r){
    		$this->success("操作成功",U('Admin/Article/linkList'));
    	}else{
    		$this->error("操作失败",U('Admin/Article/linkList'));
    	}
    }
}