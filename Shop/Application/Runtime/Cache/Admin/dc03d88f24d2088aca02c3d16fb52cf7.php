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
            <div class="navbar navbar-default">
                <div class="navbar-form">
                    <div class="pull-right">
                        <a data-original-title="返回" class="btn btn-default" title="" data-toggle="tooltip" href="javascript:history.go(-1)"><i class="fa fa-reply"></i></a>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 收货地址列表</h3>
                </div>


                <div class="panel-body">


                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>

                                <td class="text-left">
                                    收货人
                                </td>


                                <td class="text-left">
                                    联系方式
                                </td>

                                <td class="text-left">
                                    邮政编码
                                </td>

                                <td class="text-left">
                                地址
                                 </td>



                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                                    <td class="text-left"><?php echo ($list["consignee"]); ?></td>

                                    <td class="text-left"><?php echo ($list["mobile"]); ?></td>
                                    <td class="text-left">
                                        <?php echo ($list["zipcode"]); ?>
                                    </td>
                                    <td class="text-left">
                                        <!--<?php echo ($regionList[$list[province]]); ?>,<?php echo ($regionList[$list[city]]); ?>,<?php echo ($regionList[$list[district]]); ?>,<?php echo ($list["address"]); ?>-->
                                        <?php echo ($list[province]); ?>,<?php echo ($list[city]); ?>,<?php echo ($list[district]); ?>,<?php echo ($list["address"]); ?>
                                    </td>

                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    $(document).ready(function(){
        ajax_get_table('search-form2',1);

    });


    // ajax 抓取页面
    function ajax_get_table(tab,page){
        cur_page = page; //当前页面 保存为全局变量
            $.ajax({
                type : "POST",
                url:"/index.php/Admin/user/ajaxindex/p/"+page,//+tab,
                data : $('#'+tab).serialize(),// 你的formid
                success: function(data){
                    $("#ajax_return").html('');
                    $("#ajax_return").append(data);
                }
            });
    }

    // 点击排序
    function sort(field)
    {
        $("input[name='order_by']").val(field);
        var v = $("input[name='sort']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='sort']").val(v);
        ajax_get_table('search-form2',cur_page);
    }
</script>
</body>
</html>