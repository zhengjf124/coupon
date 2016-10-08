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
 


<link href="/Public/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
<script src="/Public/plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="/Public/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>

<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>          
	</ol>
</div>

    <section class="content" style="padding:0px 15px;">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-body ">
                    <ul class="nav nav-tabs">
                        <?php if(is_array($group_list)): foreach($group_list as $k=>$vo): ?><li <?php if($k == 'sms'): ?>class="active"<?php endif; ?>><a href="javascript:void(0)" data-url="<?php echo U('System/index',array('inc_type'=>$k));?>" data-toggle="tab" onclick="goset(this)"><?php echo ($vo); ?></a></li><?php endforeach; endif; ?>
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="handlepost" action="<?php echo U('System/handle');?>">
                        <!--通用信息-->
                        <div class="tab-content" style="padding:20px 0px;">
                            <div class="tab-pane active" id="tab_tongyong">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td class="col-sm-2">阿里大鱼[appkey]：</td>
                                        <td class="col-sm-8">
                                            <input type="text" class="form-control" name="sms_appkey" value="<?php echo ($config["sms_appkey"]); ?>" >
                                            <span id="err_attr_name" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>阿里大鱼[secretKey]：</td>
                                        <td >
                                            <input type="text" class="form-control" name="sms_secretKey" value="<?php echo ($config["sms_secretKey"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>公司名/品牌名/产品名：</td>
                                        <td >
                                            <input type="text" class="form-control" name="sms_product" value="<?php echo ($config["sms_product"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>短信模板名：</td>
                                        <td >
                                            <input type="text" class="form-control" name="sms_templateCode" value="<?php echo ($config["sms_templateCode"]); ?>" placeholder="写个全站通用的模板名称" >
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>注册启用短信：</td>
                                        <td>
                                            <input type="radio" class="" name="regis_sms_enable" <?php if($config['regis_sms_enable'] == 1): ?>checked<?php endif; ?> value="1" >是
                                            <input type="radio" class="" name="regis_sms_enable" <?php if($config['regis_sms_enable'] == 0): ?>checked<?php endif; ?> value="0" >否
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>短信码超时时间：</td>
                                        <td>
                                            <select name="sms_time_out">
                                                <option value="60" <?php if($config['sms_time_out'] == 60): ?>selected="selected"<?php endif; ?>>1分钟</option>
                                                <option value="120"<?php if($config['sms_time_out'] == 120): ?>selected="selected"<?php endif; ?>>2分钟</option>
                                                <option value="300"<?php if($config['sms_time_out'] == 300): ?>selected="selected"<?php endif; ?>>5分钟</option>
                                                <option value="600"<?php if($config['sms_time_out'] == 600): ?>selected="selected"<?php endif; ?>>10分钟</option>
                                                <option value="1200"<?php if($config['sms_time_out'] == 1200): ?>selected="selected"<?php endif; ?>>20分钟</option>
                                                <option value="1800"<?php if($config['sms_time_out'] == 1800): ?>selected="selected"<?php endif; ?>>30分钟</option>
                                            </select>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td><input type="hidden" name="inc_type" value="<?php echo ($inc_type); ?>"></td>
                                        <td class="text-right"><input class="btn btn-primary" type="button" onclick="adsubmit()" value="保存"></td></tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </form><!--表单数据-->
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    function adsubmit(){
        $('#handlepost').submit();
    }

    $(document).ready(function(){
        get_province();
    });

    function goset(obj){
        window.location.href = $(obj).attr('data-url');
    }
</script>
</body>
</html>