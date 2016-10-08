<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>gwshop管理后台</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.3.0 -->
 	<link href="/Public/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 --
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/Public/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
    	folder instead of downloading all of them to reduce the load. -->
    <link href="/Public/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="/Public/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />   
    <!-- jQuery 2.1.4 -->
    <script src="/Public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
	<script src="/Public/js/global.js"></script>
    <script src="/Public/js/myFormValidate.js"></script>    
    <script src="/Public/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/Public/js/layer/layer-min.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
    <script src="/Public/js/myAjax.js"></script>
    <script type="text/javascript">
    function delfunc(obj){
    	layer.confirm('确认删除？', {
    		  btn: ['确定','取消'] //按钮
    		}, function(){
    		    // 确定
   				$.ajax({
   					type : 'post',
   					url : $(obj).attr('data-url'),
   					data : {act:'del',del_id:$(obj).attr('data-id')},
   					dataType : 'json',
   					success : function(data){
   						if(data==1){
   							layer.msg('操作成功', {icon: 1});
   							$(obj).parent().parent().remove();
   						}else{
   							layer.msg(data, {icon: 2,time: 2000});
   						}
   						layer.closeAll();
   					}
   				})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);
    }
    
    function selectAll(name,obj){
    	$('input[name*='+name+']').prop('checked', $(obj).checked);
    }    
    </script>        
  </head>
  <body style="background-color:#ecf0f5;">
 

<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>          
	</ol>
</div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-list"></i> 评论回复
                        <a data-original-title="返回" class="btn btn-default pull-right" style="margin-top:-8px;" title="" data-toggle="tooltip" href="javascript:history.go(-1)"><i class="fa fa-reply"></i></a>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                    <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <!-- DIRECT CHAT PRIMARY -->
                            <div class="box direct-chat direct-chat-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">用户评论</h3>
                                    <!-- 
                                    <div class="box-tools pull-right">
                                        <span class="badge bg-light-blue" title="3 New Messages" data-toggle="tooltip">3</span>
                                        <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
                                        <button data-widget="chat-pane-toggle" title="" data-toggle="tooltip" class="btn btn-box-tool" data-original-title="Contacts"><i class="fa fa-comments"></i></button>
                                        <button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
                                    </div>
                                     -->
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <!-- Conversations are loaded here -->
                                    <div class="">
                                        <!-- Message. Default to the left -->
                                        <div class="direct-chat-msg">
                                            <div class="direct-chat-info clearfix">
                                                <span class="direct-chat-name pull-left"><!--用户名 --></span>
                                                <span class="direct-chat-timestamp pull-right"><?php echo (date("Y-m-d H:i",$comment["add_time"])); ?></span>
                                            </div><!-- /.direct-chat-info -->
                                            <img alt="<?php echo ($comment["username"]); ?>" src="/Public/dist/img/user2-160x160.jpg" class="direct-chat-img"><!-- /.direct-chat-img -->
                                            <div class="direct-chat-text">
                                                 <?php echo ($comment["content"]); ?>
                                            </div><!-- /.direct-chat-text -->
                                            <div class="">
                                                <?php if(is_array($comment["comment_img"])): $i = 0; $__LIST__ = $comment["comment_img"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><img alt="" src="<?php echo ($img); ?>" style="width:150px; height: 150px;" class=""><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </div>
                                        </div><!-- /.direct-chat-msg -->

                                       <?php if(is_array($reply)): foreach($reply as $key=>$v): ?><div class="direct-chat-msg right">
                                            <div class="direct-chat-info clearfix">
                                                <span class="direct-chat-name pull-right"><!--管理员 --></span>
                                                <span class="direct-chat-timestamp pull-left"><?php echo (date("Y-m-d H:i",$v["add_time"])); ?></span>
                                            </div><!-- /.direct-chat-info -->
                                            <img alt="管理员" src="/Public/dist/img/user2-160x160.jpg" class="direct-chat-img"><!-- /.direct-chat-img -->
                                            <div class="direct-chat-text">
                                                 <?php echo ($v["content"]); ?>
                                            </div>
                                        </div><?php endforeach; endif; ?>
                                    </div>

                                    <!-- /.direct-chat-pane -->
                                </div><!-- /.box-body -->
                                <!-- /.box-footer-->
                            </div><!--/.direct-chat -->
                        </div>       
                        <div class="col-md-2"></div>
                     <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">
                                <form method="post">
                                            <textarea class="form-control" rows="3" placeholder="请输入回复内容" name="content"></textarea>                                
			             					<div class="form-group"><button type="submit" class="btn btn-primary pull-right margin">回复</button></div>
                                </form>            
                                </div>
                                <div class="col-md-2"></div>   
                                                                                             
                                </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    // 删除操作
    function del(id,t)
    {
        if(!confirm('确定要删除吗?'))
            return false;

        location.href = $(t).data('href');
    }
</script>
</body>
</html>