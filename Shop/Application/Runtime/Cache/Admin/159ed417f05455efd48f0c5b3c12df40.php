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
 


<style>
	.form-group .control-label {
		float: left;
		width: 150px;
		padding-top: 5px;
		text-align: right;
	}

	.form-group .controls {
		margin-left: 170px;
	}

	.form-group .controls .radio {
		display: inline;
		padding-left: 0px;
		padding-right: 20px;
		vertical-align: baseline;
	}

	.form-group .controls .large {
		width: 60%;
	}

	.form-group .controls select {
		width: 60%;
	}

	.form-group .controls .form-control {
		display: inline;
	}

	.form-group .controls .help-inline {
		padding-left: 10px;
		color: #595959;
	}

	.form-actions {
		margin-left: 170px;
	}

	.dropdown-checkboxes div {
		padding: 1px;
		padding-left: 10px;
	}

	.btn {
		margin: 2px;
	}

	.pagination {
		margin: 0px 0;
	}

</style>

<div class="wrapper">
	<div class="box">
		<div class="box-header">
			<h3 class="box-title"> &nbsp;</h3>
			<div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>          
	</ol>
</div>
	
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
				<div class="row">
					<div class="col-sm-6"></div>
					<div class="col-sm-6"></div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<form action="" method="post">
							<table class="table table-bordered table-hover dataTable" id="example2" role="grid" aria-describedby="example2_info">
								<thead>
								<tr role="row">
									<th style="width: 33%" class="sorting" tabindex="0" aria-controls="example2" aria-label="Rendering engine: activate to sort column ascending">菜单名称</th>
									<th style="width: 33%" class="sorting_desc" tabindex="0" aria-controls="example2" aria-label="Browser: activate to sort column ascending" aria-sort="descending">类型</th>
									<th style="width: 33%" class="sorting" tabindex="0" aria-controls="example2" aria-label="Platform(s): activate to sort column ascending">类型值</th>
								</tr>
								</thead>
								<tbody id="tbody">
									<?php if(is_array($p_lists)): $i = 0; $__LIST__ = $p_lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><!--父级操作-->

										<tr  role="row" class="odd  pmenu<?php echo ($list["id"]); ?> menu<?php echo ($list["id"]); ?>" >
											<td>
												<input type="text" name="menu[<?php echo ($list["id"]); ?>][name]" class="topmenu" value="<?php echo ($list["name"]); ?>" placeholder="菜单名称">
												<a onclick="addcmenu(<?php echo ($list["id"]); ?>);" class="btn btn-primary"><i class="fa fa-plus"></i></a><a onclick="delmenu(<?php echo ($list["id"]); ?>);" class="btn btn-danger"><i class="fa fa-trash-o"></i></a></td>
											<td class="sorting_1">
												<select name="menu[<?php echo ($list["id"]); ?>][type]" style="width: 50%">
													<option <?php if($list['type'] == 'view'): ?>selected<?php endif; ?> value="view">链接</option>
													<option <?php if($list['type'] == 'click'): ?>selected<?php endif; ?> value="click">触发关键字</option>
													<option <?php if($list['type'] == 'scancode_push'): ?>selected<?php endif; ?> value="scancode_push">扫码</option>
													<option <?php if($list['type'] == 'scancode_waitmsg'): ?>selected<?php endif; ?> value="scancode_waitmsg"> 扫码（等待信息）</option>
													<option <?php if($list['type'] == 'pic_sysphoto'): ?>selected<?php endif; ?> value="pic_sysphoto">系统拍照发图</option>
													<option <?php if($list['type'] == 'pic_photo_or_album'): ?>selected<?php endif; ?> value="pic_photo_or_album">拍照或者相册发图</option>
													<option <?php if($list['type'] == 'pic_weixin'): ?>selected<?php endif; ?> value="pic_weixin">微信相册发图</option>
													<option <?php if($list['type'] == 'location_select'): ?>selected<?php endif; ?> value="location_select">地理位置</option>
												</select>
											</td>
											<td>
												<input style="width: 100%" type="text" value="<?php echo ($list["value"]); ?>" name="menu[<?php echo ($list["id"]); ?>][value]" placeholder="菜单值">
											</td>
											<input style="width: 100%" name="menu[<?php echo ($list["id"]); ?>][pid]" type="hidden" value="0">
										</tr>
										<!--父级操作-->

										<?php if(is_array($c_lists)): $i = 0; $__LIST__ = $c_lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$clist): $mod = ($i % 2 );++$i; if($clist['pid'] == $list['id']): ?><tr  role="row" class="odd  pmenu<?php echo ($list["id"]); ?> menu<?php echo ($clist["id"]); ?>" >
													<td <?php if($clist['pid'] > 0): ?>style="padding-left: 5em"<?php endif; ?>>
													<input type="text" name="menu[<?php echo ($clist["id"]); ?>][name]" value="<?php echo ($clist["name"]); ?>" placeholder="菜单名称">
													<a onclick="delmenu(<?php echo ($clist["id"]); ?>);" class="btn btn-danger"><i class="fa fa-trash-o"></i></a></td>
													<td class="sorting_1">
														<select name="menu[<?php echo ($clist["id"]); ?>][type]" style="width: 50%">
															<option <?php if($clist['type'] == 'view'): ?>selected<?php endif; ?> value="view">链接</option>
															<option <?php if($clist['type'] == 'click'): ?>selected<?php endif; ?> value="click">触发关键字</option>
															<option <?php if($clist['type'] == 'scancode_push'): ?>selected<?php endif; ?> value="scancode_push">扫码</option>
															<option <?php if($clist['type'] == 'scancode_waitmsg'): ?>selected<?php endif; ?> value="scancode_waitmsg"> 扫码（等待信息）</option>
															<option <?php if($clist['type'] == 'pic_sysphoto'): ?>selected<?php endif; ?> value="pic_sysphoto">系统拍照发图</option>
															<option <?php if($clist['type'] == 'pic_photo_or_album'): ?>selected<?php endif; ?> value="pic_photo_or_album">拍照或者相册发图</option>
															<option <?php if($clist['type'] == 'pic_weixin'): ?>selected<?php endif; ?> value="pic_weixin">微信相册发图</option>
															<option <?php if($clist['type'] == 'location_select'): ?>selected<?php endif; ?> value="location_select">地理位置</option>
														</select>
													</td>
													<td>
														<input style="width: 100%" type="text" value="<?php echo ($clist["value"]); ?>" name="menu[<?php echo ($clist["id"]); ?>][value]" placeholder="菜单值">
													</td>
													<input style="width: 100%" name="menu[<?php echo ($clist["id"]); ?>][pid]" type="hidden" value="<?php echo ($clist["pid"]); ?>">
												</tr><?php endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
								</tbody>

							</table>
							<button class="btn btn-primary " type="button" onclick="addpmenu()">
								添加一级菜单<i class="fa fa-plus"></i>
							</button>

							<button class="btn btn-info " type="submit">
								保存
							</button>

						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /.box-body -->
	</div>

</div>
<!--父类模板-->
<table id="parent_tpl" style="display: none">
	<!--
	<tr  role="row" class="odd  pmenu__id__ menu__id__">
  <td>
    <input type="text" name="menu[__id__][name]" value="" placeholder="菜单名称">
    <a onclick="addcmenu(__id__);" class="btn btn-primary"><i class="fa fa-plus"></i></a><a onclick="delmenu(__id__);" class="btn btn-danger"><i class="fa fa-trash-o"></i></a></td>
  <td class="sorting_1">
    <select name="menu[__id__][type]" style="width: 50%">
      	<option value="view">链接</option>
		<option value="click">触发关键字</option>
		<option value="scancode_push">扫码</option>
		<option value="scancode_waitmsg"> 扫码（等待信息）</option>
		<option value="pic_sysphoto">系统拍照发图</option>
		<option value="pic_photo_or_album">拍照或者相册发图</option>
		<option value="pic_weixin">微信相册发图</option>
		<option value="location_select">地理位置</option>
    </select>
  </td>
  <td>
    <input style="width: 100%" type="text" value="" name="menu[__id__][value]" placeholder="菜单值">
  </td>
  <input style="width: 100%" name="menu[__id__][pid]" type="hidden" value="0">
</tr>
	-->
</table>
<!--父类模板-->

<!--子类模板-->
<div id="child_tpl">
	<!--
	<tr role="row" class="odd pmenu__pid__ menu__id__" >
  <td class="" style="padding-left: 5em">
    <input type="text" name="menu[__id__][name]" value="" placeholder="菜单名称">
    <a onclick="delmenu(__id__);" class="btn btn-danger"><i class="fa fa-trash-o"></i></a></td>
  <td class="sorting_1">
    <select  name="menu[__id__][type]" style="width: 50%">
      	<option value="view">链接</option>
		<option value="click">触发关键字</option>
		<option value="scancode_push">扫码</option>
		<option value="scancode_waitmsg"> 扫码（等待信息）</option>
		<option value="pic_sysphoto">系统拍照发图</option>
		<option value="pic_photo_or_album">拍照或者相册发图</option>
		<option value="pic_weixin">微信相册发图</option>
		<option value="location_select">地理位置</option>
    </select>
  </td>
  <td>
    <input style="width: 100%" type="text" value="" name="menu[__id__][value]" placeholder="菜单值">
  </td>
  <input style="width: 100%" name="menu[__id__][pid]" type="hidden" value="__pid__">
</tr>
	-->
</div>
<!--子类模板-->

<script>
	var i  = <?php echo ($max_id); ?>;
	//添加菜单
	function addpmenu(){
		var pmenu = $('.topmenu');
//		alert(pmenu.length);
		if(pmenu.length >= 3){
			layer.alert('最多三个一级菜单', {icon: 2});  //alert('最多三个一级菜单');
			return;
		}
		i++;
		var id = i;
		var tpl = '<tr  role="row" class="odd  pmenu__id__ menu__id__"><td><input type="text" name="menu[__id__][name]" value="" placeholder="菜单名称"><a onclick="addcmenu(__id__);" class="btn btn-primary"><i class="fa fa-plus"></i></a><a onclick="delmenu(__id__);" class="btn btn-danger"><i class="fa fa-trash-o"></i></a></td><td class="sorting_1"><select name="menu[__id__][type]" style="width: 50%"><option value="view">链接</option><option value="click">触发关键字</option><option value="scancode_push">扫码</option><option value="scancode_waitmsg"> 扫码（等待信息）</option><option value="pic_sysphoto">系统拍照发图</option><option value="pic_photo_or_album">拍照或者相册发图</option><option value="pic_weixin">微信相册发图</option><option value="location_select">地理位置</option></select></td><td><input style="width: 100%" type="text" value="" name="menu[__id__][value]" placeholder="菜单值"></td><input style="width: 100%" name="menu[__id__][pid]" type="hidden" value="0"></tr>';
		tpl = tpl.replace(/__id__/g,id);
		$('#tbody').append(tpl);
	}

	function addcmenu(pid){
		var cmenu = $('.pmenu'+pid);
		if(cmenu.length >= 6){
			layer.alert('一级菜单下最多5个二级菜单', {icon: 2});  //alert('一级菜单下最多5个二级菜单');
			return;
		}
		i++;
		var id = i;
		var tpl = '<tr role="row" class="odd pmenu__pid__ menu__id__" ><td class="" style="padding-left: 5em"><input type="text" name="menu[__id__][name]" value="" placeholder="菜单名称"><a onclick="delmenu(__id__);" class="btn btn-danger"><i class="fa fa-trash-o"></i></a></td><td class="sorting_1"><select  name="menu[__id__][type]" style="width: 50%"><option value="view">链接</option><option value="click">触发关键字</option><option value="scancode_push">扫码</option><option value="scancode_waitmsg"> 扫码（等待信息）</option><option value="pic_sysphoto">系统拍照发图</option><option value="pic_photo_or_album">拍照或者相册发图</option><option value="pic_weixin">微信相册发图</option><option value="location_select">地理位置</option></select></td><td><input style="width: 100%" type="text" value="" name="menu[__id__][value]" placeholder="菜单值"></td><input style="width: 100%" name="menu[__id__][pid]" type="hidden" value="__pid__"></tr>';
		tpl = tpl.replace(/__id__/g,id);
		tpl = tpl.replace(/__pid__/g,pid);
		$(cmenu.last()).after(tpl);
	}

	function delmenu(id){
		if(!confirm("确定删除吗？")){
			return;
		}
		$.ajax({
			url:'/index.php?m=Admin&c=Wechat&a=del_menu&id='+id,
			type:'get',
			success:function(data){
				if(data=='success'){
					//删除子分类
					$('.pmenu'+id).remove();
					$('.menu'+id).remove();
				}else{
					layer.msg('删除失败');
				}
			}
		});
		/*
		//删除子分类
		$('.pmenu'+id).remove();
		$('.menu'+id).remove();
		*/
	}

</script>
</body>
</html>