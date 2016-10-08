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
    <!-- Content Header (Page header) -->
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

                    <!--表单数据-->
                    <form method="post" id="handlepost" action="">
                        <!--通用信息-->
                        <div class="tab-content col-md-10">
                            <div class="tab-pane active" id="tab_tongyong">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>URL(服务器地址)：</td>
                                        <td >
                                            <input readonly type="text" class="form-control"  placeholder="请先以下信息保存后显示" value="<?php echo ($apiurl); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Token：</td>
                                        <td >
                                            <input type="text" class="form-control" name="w_token" value="<?php echo ($wechat["w_token"]); ?>" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2">公众号名称：</td>
                                        <td class="col-sm-8 ">
                                            <input type="text" class="form-control" name="wxname" value="<?php echo ($wechat["wxname"]); ?>" required />
                                            <span id="err_attr_name" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>公众号原始id：</td>
                                        <td >
                                            <input type="text" class="form-control" name="wxid" value="<?php echo ($wechat["wxid"]); ?>" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>微信号：</td>
                                        <td >
                                            <input type="text" class="form-control" name="weixin" value="<?php echo ($wechat["weixin"]); ?>" required />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>头像地址：</td>
                                        <td >
                                            <input type="text" id="headerpic" class="form-control" name="headerpic" value="<?php echo ($wechat["headerpic"]); ?>" required />
                                            <input onclick="GetUploadify(1,'headerpic','weixin');"  type="button" value="上传头像"/>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>AppID：</td>
                                        <td >
                                            <input type="text" class="form-control" name="appid" value="<?php echo ($wechat["appid"]); ?>" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>AppSecret：</td>
                                        <td >
                                            <input type="text" class="form-control" name="appsecret" value="<?php echo ($wechat["appsecret"]); ?>" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>二维码：</td>
                                        <td >
                                            <input type="text" class="form-control" id="qr" name="qr" value="<?php echo ($wechat["qr"]); ?>" required />
                                            <input onclick="GetUploadify(1,'qr','weixin');"  type="button" value="上传头像"/>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td>微信号类型：</td>
                                        <td >
                                            <select name="type">
                                                <option <?php if($wechat['type'] == 1): ?>selected<?php endif; ?> value="1">订阅号</option>
                                                <option <?php if($wechat['type'] == 2): ?>selected<?php endif; ?> value="2">认证订阅号</option>
                                                <option <?php if($wechat['type'] == 3): ?>selected<?php endif; ?> value="3">服务号</option>
                                                <option <?php if($wechat['type'] == 4): ?>selected<?php endif; ?> value="4">认证服务号</option>
                                            </select>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td><input type="hidden" name="id" value="<?php echo ($wechat["id"]); ?>"></td>
                                        <td class="text-right"><input class="btn btn-primary" type="submit" value="保存"></td></tr>
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
</body>
</html>