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
                        <?php if(is_array($group_list)): foreach($group_list as $k=>$vo): ?><li <?php if($k == 'basic'): ?>class="active"<?php endif; ?>><a href="javascript:void(0)" data-url="<?php echo U('System/index',array('inc_type'=>$k));?>" data-toggle="tab" onclick="goset(this)"><?php echo ($vo); ?></a></li><?php endforeach; endif; ?>                        
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="handlepost" action="<?php echo U('System/handle');?>">                    
                        <!--通用信息-->
                    <div class="tab-content" style="padding:20px 0px;">                 	  
                        <div class="tab-pane active" id="tab_tongyong">                           
                            <table class="table table-bordered">
                                <tbody> 
                                <tr>
                                    <td>会员注册赠送积分：</td>
                                    <td><input type="number"  pattern="^\d{1,}$" title="只能输入数字"  class="input-sm" name="reg_integral" value="<?php echo ($config["reg_integral"]); ?>" ></td>
                               		<td></td>
                                </tr>   
                              	<tr>
                                    <td>附件上传大小：</td>
                                    <td>                         		
                                        <select class="input-sm" name="file_size">
                                            <option value="1" <?php if($config[file_size] == 1): ?>selected="selected"<?php endif; ?>>1M</option>
                                            <option value="2" <?php if($config[file_size] == 2): ?>selected="selected"<?php endif; ?>>2M</option>
                                            <option value="3" <?php if($config[file_size] == 3): ?>selected="selected"<?php endif; ?>>3M</option>
                                            <option value="5" <?php if($config[file_size] == 4): ?>selected="selected"<?php endif; ?>>5M</option>
                                            <option value="10"<?php if($config[file_size] == 10): ?>selected="selected"<?php endif; ?>>10M</option>
                                            <option value="50"<?php if($config[file_size] == 50): ?>selected="selected"<?php endif; ?>>50M</option>
                                            <option value="100"<?php if($config[file_size] == 100): ?>selected="selected"<?php endif; ?>>100M</option>
                                        </select>
                                    </td>
                                    <td></td>
                                </tr>
                                
                                <tr>
                                    <td>默认库存：</td>
                                    <td><input type="number"  pattern="^\d{1,}$" title="只能输入数字"  class="input-sm" name="default_storage" value="<?php echo ($config["default_storage"]); ?>" ></td>
                                	<td></td>
                                </tr>
                                
                                <tr>
                                    <td>库存预警数：</td>
                                    <td><input type="number"  pattern="^\d{1,}$" title="只能输入数字"  class="input-sm" name="warning_storage" value="<?php echo ($config["warning_storage"]); ?>" ></td>
                                	<td>当商品数量少于X件时，会在系统后台首页<库存预警>显示</td>
                                </tr>
                                <tr>
                                	<td>发票税率：</td>
                                	<td><input type="number" class="input-sm" name="tax">%</td>
                                	<td>当买家需要发票的时候就要增加<商品金额>*<税率>的费用</td>
                                </tr>   
                                <!--                                         
                            	<tr>
                                    <td>是否开启订单提醒：</td>
                                    <td >
                                       	 是<input type="radio" class="" name="is_remind" value="0" <?php if($config[is_remind] == 0): ?>checked="checked"<?php endif; ?>>
                                       	 否<input type="radio" class="" name="is_remind" value="1" <?php if($config[is_remind] == 1): ?>checked="checked"<?php endif; ?>>                               
                                    </td>
                                	<td></td>
                                </tr>
                                <tr>
                                	<td>发货后订单自动完成时间：</td>
                                	<td><input type="number" class="input-sm" name="order_finish_time">天</td>
                                	<td>发货后的X天，会自动变为完成状态</td>
                                </tr> 
                                <tr>
                                	<td>未付款订单自动取消时间：</td>
                                	<td><input type="number" class="input-sm" name="order_cancel_time">天</td>
                                	<td>未付款订单生成X天，会自动被取消掉</td>
                                </tr>
                                -->
                                <tr>
                                	<td>首页热门搜索词：</td>
                                	<td colspan="2"><input type="text" class="form-control" name="hot_keywords" value="<?php echo ($config["hot_keywords"]); ?>">
                                		<p class="text-warning">多个热门搜索关键词请用竖线|分开，如(手机|电脑)</p>
                                	</td>
                                </tr>      
                            	<tr>
                                    <td>开启APP测试：</td>
                                    <td >
                                       	 关<input type="radio" class="" name="app_test" value="0" <?php if($config[app_test] == 0): ?>checked="checked"<?php endif; ?>>
                                       	 开<input type="radio" class="" name="app_test" value="1" <?php if($config[app_test] == 1): ?>checked="checked"<?php endif; ?>>                               
                                    </td>
                                	<td></td>
                                </tr>                                
                                </tbody> 
                                <tfoot>
                                	<tr>
                                	<td><input type="hidden" name="inc_type" value="<?php echo ($inc_type); ?>"></td>
                                	<td></td>
                                	<td class="text-right"><input class="btn btn-primary" type="button" onclick="adsubmit()" value="保存"></td>
                                	</tr>
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
	//get_province();
});

function goset(obj){
	window.location.href = $(obj).attr('data-url');
}
</script>
</body>
</html>