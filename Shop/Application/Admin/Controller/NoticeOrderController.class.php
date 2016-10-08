<?php
/**
 * wbz
 */

namespace Admin\Controller;

class NoticeOrderController extends BaseController {

    public function index(){
        $this->display();
    }
    
    public function addSales(){
    	$act = I('GET.act','add');
    	$this->assign('act',$act);
    	$topic_id = I('GET.id');

    	if($topic_id){
    		$topic_info = M('noticeSales')->where('id='.$topic_id)->find();
    		$this->assign('info',$topic_info);
    	}
    	
//    	$this->assign("URL_upload", U('Admin/Ueditor/imageUp',array('savepath'=>'topic')));
//    	$this->assign("URL_fileUp", U('Admin/Ueditor/fileUp',array('savepath'=>'topic')));
//    	$this->assign("URL_scrawlUp", U('Admin/Ueditor/scrawlUp',array('savepath'=>'topic')));
//    	$this->assign("URL_getRemoteImage", U('Admin/Ueditor/getRemoteImage',array('savepath'=>'topic')));
//    	$this->assign("URL_imageManager", U('Admin/Ueditor/imageManager',array('savepath'=>'topic')));
//    	$this->assign("URL_imageUp", U('Admin/Ueditor/imageUp',array('savepath'=>'topic')));
//    	$this->assign("URL_getMovie", U('Admin/Ueditor/getMovie',array('savepath'=>'topic')));
//    	$this->assign("URL_Home", "");
    	$this->display();
    }
    
    public function sales(){
    	$notice_sales =  M('noticeSales');
        $keywords = I('keywords');
        $where['title'] = array('like','%'.$keywords.'%');
    	$res = $notice_sales->where($where)->order('add_time')->page($_GET['p'].',10')->select();
    	if($res){
    		foreach ($res as $val){
    			$val['state'] = $val['state']>1 ? '已发布' : '未发布';
    			$val['add_time'] = date('Y-m-d H:i',$val['add_time']);
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);// 赋值数据集
    	$count = $notice_sales->where($where)->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
    	$show = $Page->show();// 分页显示输出
    	$this->assign('page',$show);// 赋值分页输出
    	$this->display('salesList');
    }
    
    public function SalesHandle(){
    	$data = I('post.');       
        //$data['topic_content'] = $_POST['topic_content']; // 这个内容不做转义
    	if($data['act'] == 'add'){
    		$data['add_time'] = time();
    		$r = D('noticeSales')->add($data);
    	}
    	if($data['act'] == 'edit'){
    		$r = D('noticeSales')->where('id='.$data['id'])->save($data);
    	}
    	 
    	if($data['act'] == 'del'){
    		$r = M('noticeSales')->where('id='.$data['id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	 
    	if($r){
    		$this->success("操作成功",U('Admin/NoticeOrder/sales'));
    	}else{
    		$this->error("操作失败",U('Admin/NoticeOrder/sales'));
    	}
    }
}