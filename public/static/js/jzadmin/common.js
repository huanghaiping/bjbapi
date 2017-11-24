// JavaScript Document
var chn=function(cid,op){
  if(op=="show"){
	  $("tr[pid='"+cid+"']").each(function(){
		  $(this).removeAttr("status").show();
		  chn($(this).attr("id"),"show");
	  });
  }else{
	  $("tr[pid='"+cid+"']").each(function(){
		  $(this).attr("status",1).hide();
		  chn($(this).attr("id"),"hide");
	  });
  }
}

$(function(){
 
	//复选框内容控制
	$('table th input:checkbox').on('click' , function(){
		var that = this;
		$(this).closest('table').find('tr > td:first-child input:checkbox')
		.each(function(){
			this.checked = that.checked;
			$(this).closest('tr').toggleClass('selected');
		});	
	});
	
	$('tr > td:first-child input').on('click',function(){
		$(this).closest('tr').toggleClass('selected');	
		 
	});
	
	//折叠树状的列表
	$("table tr .sort_tree").on('click',function(){
		if($(this).attr("status")!=1){
			chn($(this).parent().attr("id"),"hide");
			$(this).attr("status",1).attr("title","点击展开");
		}else{
			chn($(this).parent().attr("id"),"show");
			$(this).removeAttr("status").attr("title","点击折叠");
		}
 	 });
	 //提示框
	 $('[data-rel=tooltip]').tooltip();
	 $('[data-rel=popover]').popover({html:true});
	 
	//快捷改变操作排序dblclick
	$("tbody>tr>td[fd]").on("click",function(){ 
			var inval = $(this).html();
			var infd = $(this).attr("fd"); 
			var keys = $(this).attr("key"); 
			var inid =  $(this).parents("tr").attr("id");
			if($(this).attr('edit')==0){
			 $(this).attr('edit','1').html("<input class='input' style='width:30px;' size='5' id='edit_"+infd+"_"+inid+"' value='"+inval+"' />").find("input").select();
			}
			$("#edit_"+infd+"_"+inid).focus().bind("blur",function(){
				var editval = $(this).val();
				$(this).parents("td").html(editval).attr('edit','0');
				if(inval!=editval){
					$.post(__updatesort__,{"ids":inid,'field':infd,"value":editval,"model":_MODEL_TABLE_,"cache":_CACHE_NAME_,'keys':keys});
				}
			})
	 });
	 
	 	//快捷启用禁用操作
		$(".opStatus").on("click",function(){
			var obj=$(this);
			var id=$(this).parents("tr").attr("id");
			var status=$(this).attr("val"); 
			 var keys = $(this).attr("key"); 
			$.post(__setStatus__, { "ids":id, "status":status==1?0:1,"model":_MODEL_TABLE_,"cache":_CACHE_NAME_,"keys":keys}, function(json){
				if(json.code==1){
					//layer.alert(json.msg);
					$(obj).attr("val",status==1?0:1).html(status==0 ?'<span class="green">'+json.msg+'</span>':'<span class="red">'+json.msg+'</span>');
				}else{
					//layer.alert(json.msg);
				}
			});
		});
		
		//删除单条记录行
		$(".delete_row").on("click",function(){
				var url=$(this).attr("link");
				var $this=$(this);
				layer.confirm('确定要删除?', { icon:3,
				  btn: ['确定','取消'] //按钮
				}, function(){
					$.get(url,function(data){
						if(data.code==1){
						  layer.msg(data.msg, {icon: 1});
						}else{
						  layer.msg(data.msg, {icon: 2});
						}
						setTimeout("window.location.reload();",2000);
					});
				});

		});
		
		//批量删除
		$(".delestatus").click(function(){
			  var ids=[];
			  $("tbody input[type='checkbox'][name='ids[]']:checked").each(function(i){
					ids[i]=$(this).val();
			   });
			   if(ids==""){
					layer.msg('请选择要删除的内容', {icon: 2});
					return false;	
			  }
			  var links=$(this).attr("link");
			  layer.confirm('确定要删除所有内容?', {
				  btn: ['确定','取消'] //按钮
				}, function(){
					$.post(links,{"ids" : ids},function(data){
						if(data.code==1){
						  layer.msg(data.msg, {icon: 1});
						}else{
						  layer.msg(data.msg, {icon: 2});
						}
						setTimeout("window.location.reload();",2000);
					});
				});   
		 });
		 
		 //图片显示大图
		var x = 10;
		var y = 20;
		$("a.image_tip").mouseover(function(e){
			this.myTitle = this.title;
			this.title = "";	
			var imgTitle = this.myTitle? "<br/>" + this.myTitle : "";
			var tooltip = "<div id='tooltip'><img src='"+ this.href +"' alt='预览大图'/>"+imgTitle+"<\/div>"; //创建 div 元素
			$("body").append(tooltip);	//把它追加到文档中						 
			$("#tooltip")
				.css({
					"top": (e.pageY+y) + "px",
					"left":  (e.pageX+x)  + "px"
				}).show("fast");	  //设置x坐标和y坐标，并且显示
		}).mouseout(function(){
			this.title = this.myTitle;	
			$("#tooltip").remove();	 //移除 
		}).mousemove(function(e){
			$("#tooltip")
				.css({
					"top": (e.pageY+y) + "px",
					"left":  (e.pageX+x)  + "px"
				});
		});			
		
		
})

/**
 * [dialogIframe iframe弹窗]
 * @param  {[type]} url   [iframe地址]
 * @param  {[type]} title [标题]
 */
function dialogIframe(url,title){
     var url = url + "?iframe=" + window.name+"&ver="+Math.random();
    layer.open({
        title:title,
        type:2,
        area: ['60%'],
        maxmin:true,
		//zIndex:1989101400,
		scrollbar:false,
		shadeClose:true,
		fix :true,
        content:url,
		success: function(layero, index) {
			layer.style(index, {
				top: '10%'
			});
			layer.iframeAuto(index);
		}
    });
}

/**
 * [closeIframe 关闭父级iframe]
 */
function closeIframe(){
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
}

/**
 * [delFiles 删除文件]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function delFiles (obj,url,field) {
	var img_url=$(obj).siblings('img').attr('delfile'); 
	var json = {"url":img_url};
	$.post(url, json, function(data, textStatus, xhr) {
		if(data.status){
			$(obj).parent('div').remove();
			$('#'+field).val('');
		} else {
			parent.layer.msg(data.info,{icon:2,time:2000,shade: [0.3,'#000']});
		}

	});
}