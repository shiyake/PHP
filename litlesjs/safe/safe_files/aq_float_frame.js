var CAqCommFrame=(function(){var UnionFrameUrl="/cn2/unionverify/unionverify_jump";var ReqMethod="get";var FormData={};var HistoryPage=new Array();var CurPageIndex=-1;var JSString="";var CloseButId="frame_close";var is_IE6=false;var _top=0;var _left=0;var showCommFrame=function(frameAttr){isIE6();aqCommShowFullDiv("aqCommFullDiv");Init();FrameContent(frameAttr);$("#aq_comm_frame").css({position:"fixed",zIndex:"10002",left:"50%",top:"50%"});if(is_IE6){$("#aq_comm_frame").css("position","absolute");setCenter()}else{var FrameMarginTop=-document.getElementById("aq_comm_frame").offsetHeight/2+"px";var FrameMarginLeft=-document.getElementById("aq_comm_frame").offsetWidth/2+"px";$("#aq_comm_frame").css({marginTop:FrameMarginTop,marginLeft:FrameMarginLeft})}$("#comm_frame_title").html(frameAttr.title);$("#aq_comm_frame").css("visibility","")};var Init=function(){CurPageIndex=-1;var aq_comm_frame=$("<div/>").attr("id","aq_comm_frame").addClass("round").css("visibility","hidden");var round_top=$("<div/>").addClass("r_top");aq_comm_frame.append(round_top);var container=$("<div/>").addClass("round_container");aq_comm_frame.append(container);var title_div=$("<div/>").addClass("r_title");container.append(title_div);title_div.append($("<span/>").attr("id","comm_frame_title"));title_div.append($("<a/>").attr("href","javascript:;").attr("id",CloseButId).addClass("close").bind("click",closeCommFrame));var frame_content=$("<div/>").attr("id","frame_content");container.append(frame_content);var round_bottom=$("<div/>").addClass("r_bottom");aq_comm_frame.append(round_bottom);for(i=4;i>0;i--){var b=$("<b/>").addClass("b"+i);aq_comm_frame.append(b)}$("body").append(aq_comm_frame)};var Next=function(frameAttr){FrameContent(frameAttr);$("#aq_comm_frame").show()};var goBack=function(){if(HistoryPage[CurPageIndex]&&HistoryPage[CurPageIndex].type&&HistoryPage[CurPageIndex].type=="div"){removeDivBack(CurPageIndex);CurPageIndex--;removeDiv2Frame(HistoryPage[CurPageIndex].id)}else{CurPageIndex--;$("#frame_content").html(HistoryPage[CurPageIndex].content)}};var submit=function(form_id,submit_data){if(typeof submit_data=="undefined"){getFormData(form_id)}else{FormData=submit_data}var submit_method=$("#"+form_id).attr("method");submit_method=submit_method==""?"get":submit_method;var submit_action=$("#"+form_id).attr("action");$.ajax({url:submit_action,type:submit_method,async:false,timeout:100,dataType:"text",data:FormData,error:function(){},success:function(msg){pageChageSuccCallback(msg)}})};var FrameContent=function(frameAttr){removeDivBack(CurPageIndex);if(frameAttr.type=="iframe"){aqCommRemoveElement("embed_comm_frame");var EmbedFrame=$("<iframe frameborder='0' scrolling='no' marginheight='0' marginwidth='0' id='embed_comm_frame' style='margin:0 auto;width:403px;height:312px;'></iframe>");var union_dst_url="";if(frameAttr.url){union_dst_url=frameAttr.url}else{EmbedFrame.css({height:"283px"});union_dst_url=UnionFrameUrl+"?jumpname="+frameAttr.jumpname+"&PTime="+Math.random()}$("#frame_content>*").hide();$("#frame_content").append(EmbedFrame);EmbedFrame.attr("src",union_dst_url)}else{if(frameAttr.type=="div"&&$("#"+frameAttr.id).length>0){CurPageIndex++;HistoryPage[CurPageIndex]={content:$("#"+frameAttr.id).html(),type:"div",id:frameAttr.id};removeDiv2Frame(frameAttr.id)}else{if(frameAttr.type=="ajax"){$.ajaxSetup({cache:false});$.ajax({url:frameAttr.url,type:ReqMethod,async:false,timeout:100,dataType:"text",error:function(){},success:function(msg){$.ajaxSetup({cache:true});pageChageSuccCallback(msg)}})}}}};var closeCommFrame=function(){if(typeof OnFrameClose=="function"){OnFrameClose()}removeDivBack(CurPageIndex);if(is_IE6){$(window).unbind("resize");$(window).unbind("scroll")}$.ajaxSetup({cache:false});$("#aq_comm_frame").hide();aqCommRemoveElement("aqCommFullDiv");aqCommRemoveElement("embed_comm_frame");aqCommRemoveElement("aq_comm_frame")};var aqCommRemoveElement=function(id){if($("#"+id).length>0){$("#"+id).remove()}};var OnUnionBack=function(tourl){Next({type:"ajax",url:tourl})};var pageChageSuccCallback=function(msg){if(typeof OnFrameClose=="function"){OnFrameClose="undefined"}CurPageIndex++;HistoryPage[CurPageIndex]={content:msg,type:"ajax"};$("#frame_content").html(msg)};var aqCommShowFullDiv=function(id){aqCommRemoveElement(id);var fullDiv=$("<div/>").css({zIndex:"1000",left:"0",top:"0",position:"fixed",width:"100%",height:"100%"}).attr("id","aqCommFullDiv");if(is_IE6){fullDiv.css("position","absolute");$(window).bind("resize",function(){var fwidth=Math.max(document.documentElement.scrollWidth,document.documentElement.clientWidth)+"px";var fheight=Math.max(document.documentElement.scrollHeight,document.documentElement.clientHeight)+"px";$(this).css({width:fwidth,height:fheight})})}if($.browser.msie){fullDiv.css({filter:"alpha(opacity:0)",backgroundColor:"#fff"})}else{fullDiv.css({background:"transparent"})}$("body").append(fullDiv)};var getFormData=function(formid){var input;var form_obj=$("#"+formid+" input[type='text'][disabled!=true]");for(var i=0;i<form_obj.length;i++){FormData[form_obj.eq(i).attr("name")]=form_obj.eq(i).val()}var form_obj=$("#"+formid+" input[type='hidden']");for(var i=0;i<form_obj.length;i++){FormData[form_obj.eq(i).attr("name")]=form_obj.eq(i).val()}form_obj=$("#"+formid+" select[disabled!=true]");for(var i=0;i<form_obj.length;i++){FormData[form_obj.eq(i).attr("name")]=form_obj.eq(i).val()}form_obj=$("#"+formid+" input[type='radio'][disabled!=true][checked=true]");for(var i=0;i<form_obj.length;i++){FormData[form_obj.eq(i).attr("name")]=form_obj.eq(i).val()}form_obj=$("#"+formid+" input[type='checkbox'][disabled!=true][checked=true]");for(var i=0;i<form_obj.length;i++){FormData[form_obj.eq(i).attr("name")]="on"}};var removeDiv2Frame=function(div_id){var frame_div_tmp=$("#"+div_id).html();$("#"+div_id).empty();$("#frame_content").html(frame_div_tmp)};var removeDivBack=function(page_index){if(HistoryPage[page_index]&&HistoryPage[page_index].type&&HistoryPage[page_index].type=="div"){document.getElementById(HistoryPage[page_index].id).innerHTML=HistoryPage[page_index].content}};var isIE6=function(){var isIE=(document.all)?true:false;is_IE6=isIE&&([/MSIE (\d)\.0/i.exec(navigator.userAgent)][0][1]==6);return is_IE6};var setFixed=function(){var obj=$("#aq_comm_frame");var frame_top=document.documentElement.scrollTop-_top+document.getElementById("aq_comm_frame").offsetTop+"px";var frame_left=document.documentElement.scrollLeft-_left+document.getElementById("aq_comm_frame").offsetLeft+"px";_top=document.documentElement.scrollTop;_left=document.documentElement.scrollLeft;obj.css({top:frame_top,left:frame_left})};var setCenter=function(){var FrameMarginTop=document.documentElement.scrollTop-document.getElementById("aq_comm_frame").offsetHeight/2+"px";var FrameMarginLeft=document.documentElement.scrollLeft-document.getElementById("aq_comm_frame").offsetWidth/2+"px";$("#aq_comm_frame").css({marginTop:FrameMarginTop,marginLeft:FrameMarginLeft})};var execJS=function(html_doc){var jscode=/<script[^\/]*>((\s|\S)+)<\/script>/i.exec(html_doc);if(jscode!=null){for(var i=1;i<jscode.length;i++){eval(jscode[i])}}};var getScriptFromHtml=function(html_doc){var jscode=/<script[^\/]*>((\s|\S)+)<\/script>/i.exec(html_doc);if(jscode!=null){for(var i=1;i<jscode.length;i++){JSString+=jscode[i]}}};return{show:showCommFrame,Next:Next,goBack:goBack,close:closeCommFrame,OnUnionBack:OnUnionBack,submit:submit,CloseButId:CloseButId}})();