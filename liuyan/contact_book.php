<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>社长网站-留言内容</title>
<link id="css" href="css/public.css" rel="stylesheet" type="text/css">

<style type="text/css">
<!--
.title { margin:0px 40px 0px 0px; padding:3px 3px 1px 3px; border-bottom:1px #CECECE solid; font-size:12px; background-color:#E4F2FF; }
.title img { margin-left:5px; }
.texts { margin:0px 40px 5px 0px; padding:3px 3px 10px 3px; color:#6666FF; }
.abt { background-color:#FFD891; border:1px outset; }
.ad_reply { color:#999999; }
#page { font-size:12px; }
.m { float:right; display:none; }
-->
</style>
<script language="javascript" src="js/set.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
<!--
//删除
var managePower=getCookie('manage');
if(managePower==1){
  document.writeln('<style type="text/css"><!-- ');
  document.writeln('.m { display:block; }');
  document.writeln('--></style>');
}
//删除
function del(){
  if(managePower==1){
    if(confirm('确定删除么？！')){
	  document.manageform.submit();
    }
  }else{
    var str_n=prompt('操作越权！请输入管理密码：','');
    if(str_n){
      document.getElementById('lastFrame').src='runpost.php?act=login&password='+str_n+'';
    }
  }
}
//回复
function reply(id,title){
  if(managePower==1){
    document.getElementById('reply').innerHTML='\
<form id="replyform" name="replyform" method="post" action="runpost.php?act=reply&id='+id+'" onsubmit="return postChk(this)" target="lastFrame">\
  <p>回复：<font color=gray>'+title+'</font></p>\
  <p><textarea name="content" rows="12" cols="92"></textarea></p>\
  <p><input type="submit" value=" 回 复 " name="submit" /></p>\
</form>';
  }else{
    var str_n=prompt('操作越权！请输入管理密码：','');
    if(str_n){
      document.getElementById('lastFrame').src='runpost.php?act=login&password='+str_n+'';
    }
  }
}

function postChk(theForm){
  var con=theForm.content.value.replace(/^\s+|\s+$/g,'');
  if(con==''){
    alert('回复内容不能为空白！');
	theForm.content.focus();
    return false;
  }
  return true;
}

-->
</script>



</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="logo">
  <tr>
    <!-- logo_style --><td width="200" valign="top" align="center">
      <a href="http://www.ehuowu.com/" target="_self"><img src="img/logo.png" alt="logo" width="350" height="72" /></a></td>
    <!-- /logo_style --><td width="9"></td>
</table>
<div id="banner" align="left"><a style="float:right" href="http://huajishe.tk" ><strong>返回主页</strong></a><a href="index.html" target="_self">留言</a> &gt; 留言查看</div>

<div class="t1"><br />
<iframe id="lastFrame" name="lastFrame" frameborder="0" style="display:none"></iframe>
<?php

if(!file_exists('data') || !file_exists('data/index.txt') || !file_exists('data/file.php')){
  die('查不到数据库文件！</body></html>');
}
require_once('set.php');
$type=array(1=>'[意见建议]',2=>'[挑错本站]',3=>'[站长闲聊]',4=>'[广告服务]');
$text='';

$size=@filesize('data/index.txt');
$alln=ceil($size/13); //数量
$p=get_page($alln); //页码

?>


<div style="height:30px; clear:both;">

<select name="showtype" style="float:right; margin-right:40px;" onchange="location.href='?showtype='+this.value+'&p=<?php echo $p; ?>';">
<option value="1" <?php echo ($_REQUEST['showtype']!=2)?' selected':''; ?>>默认顺序</option>
<option value="2" <?php echo ($_REQUEST['showtype']==2)?' selected':''; ?>>反序查看</option>
</select>
<a href="contact.html" class="abt">&nbsp;发 表 留 言&nbsp;</a>
<script language="javascript" type="text/javascript">
<!--
if(getCookie('manage')==1){
  document.write('<a href="runpost.php?act=logout" class="abt" target="lastFrame">&nbsp;退 出&nbsp;</a>');
}
-->
</script>


</div><br />
<form action="runpost.php?act=del" method="post" name="manageform" target="lastFrame">
<?php


//页数
function get_page($n){
  global $web;
  $_REQUEST['p']=abs($_REQUEST['p']);
  if(!$_REQUEST['p'] || $_REQUEST['p']<1){
    return 1;
  }elseif($n && $_REQUEST['p']>ceil($n/$web['pagesize'])){
    return ceil($n/$web['pagesize']);
  }else{
    return floor($_REQUEST['p']);
  }
}

//页码
function get_page_foot($p,$totallists,$t){
  global $web;
  $text='';
  if($totallists>0 && ($pagesize=abs($web['pagesize']))){
    $totalpages=ceil($totallists/$pagesize);
    if($totalpages==1){
      $text.='第一页 上一页 下一页 最后页';
    }else{
      $first='?p=1'.$t.'';
      $up='?p='.($p-1).''.$t.'';
      $down='?p='.($p+1).''.$t.'';
      $end='?p='.$totalpages.''.$t.'';
      $go='?p=\'+pId.value+\''.$t.'';
      if($p==1){
        $text.='第一页 上一页 <a href="'.$down.'">下一页</a> <a href="'.$end.'">最后页</a>';
      }elseif($p==$totalpages){
        $text.='<a href="'.$first.'">第一页</a> <a href="'.$up.'">上一页</a> 下一页 最后页';
      }else{
        $text.='<a href="'.$first.'">第一页</a> <a href="'.$up.'">上一页</a> <a href="'.$down.'">下一页</a> <a href="'.$end.'">最后页</a>';
      }
      $text2=' <input id="pageGo" name="pageGo" type="text" size="3" /><input type="button" value="跳至" onclick="var pId=document.getElementById(\'pageGo\');if(!isNaN(pId.value)&&pId.value>=1&&pId.value<='.$totalpages.')location.href=\''.$go.'\';" />';
    }
    return '<br /><br />
  <div id="page">
  共'.$totallists.'条 <font color="#FF6600"> '.$p.'</font>/'.$totalpages.'页 共'.$totallists.'条 '.$text.' 
   '.$text2.'
  </div>';
  }
}

if(!function_exists('str_split')){ 
  function str_split($str,$len=1){
    $arr=array();
    for($i=0;$i<strlen($str);$i+=$len){
      $arr[]=substr($str,$i,$len);
    }
    return $arr;
  }
}

//从数据库取列表id
function get_list($file,$sta,$len){
  $list=array();
  if(file_exists($file) && ($fp=@fopen($file,'rb'))){
    @fseek($fp,-$sta*13,SEEK_END);
    $line=@fread($fp,$len*13);
    @fclose($fp);
    $list=@str_split($line,13);
    $list=array_reverse($list);
  }
  return $list;
}

function l_list($str,$id){
  global $type;
  $text='';
  $rows=@explode('|',$str);
  $text.='
<div class="title"><input name="id[]" id="id[]" class="m" type="checkbox" value="'.$id.'" /><a href="#" onclick="del();return false;" title="删除"><img src="img/b_de.gif" align="right" /></a><a href="#reply" onclick="reply(\''.$id.'\',\''.$rows[0].' - '.$type[$rows[1]].'\');" title="回复"><img src="img/b_re.gif" align="right" /></a>'.$rows[0].' - '.$type[$rows[1]].'</div>
<div class="texts">'.$rows[2].''.((trim($rows[3])!='')?'<br /><span class="ad_reply"><b>[站长回复]</b>'.$rows[3].'</span>':'').'</div>';
  return $text;
}







//-------------------------------------------------------------------------------------------------------------------

if($web['pagesize']*$p>=$alln){
  $start=$alln;
  $end=$alln-$web['pagesize']*($p-1);
}else{
  $start=$web['pagesize']*$p;
  $end=$web['pagesize'];
}  
$list1=get_list('data/index.txt',$start,$end);

if($web['showtype']==2){
  if($_REQUEST['showtype']==2){
    $list=$list1;
  }else{
    $list=array_reverse($list1);
  }
}else{
  if($_REQUEST['showtype']==2){
    $list=array_reverse($list1);
  }else{
    $list=$list1;
  }
}



if($alln>0){
  if($fp=@fopen('data/file.php','rb')){
    foreach($list as $bbs){
      $rows=@explode('|',trim(trim($bbs),'.'));
      @fseek($fp,$rows[0]);
      $str=@fread($fp,$rows[1]);
      $text.=l_list($str,$rows[0]);
      unset($rows,$str);
    }

    @fclose($fp);
    $text.=get_page_foot($p,$alln,'&showtype='.$_REQUEST['showtype']);
  }
}else{
  $text.='暂无留言数据';
}






echo $text;

?>
	</form>
<br />

<div id="reply">



</div>


</div>
<br />

<div id="foot"><a href="http://huajishe.tk" >返回主页</a></div>
<script src="https://eqcn.ajz.miesnfu.com/wp-content/plugins/wp-3d-pony/live2dw/lib/L2Dwidget.min.js"></script>
<script>
    L2Dwidget.init({
        "model": {
　　　　　　　//jsonpath控制显示那个小萝莉模型，下面这个就是我觉得最可爱的小萝莉模型
            jsonPath: "https://unpkg.com/live2d-widget-model-wanko@1.0.5/assets/wanko.model.json",
            "scale": 1
        },
        "display": {
            "position": "right", //看板娘的表现位置
            "width": 150,  //小萝莉的宽度
            "height": 150, //小萝莉的高度
            "hOffset": 0,
            "vOffset": -20
        },
        "mobile": {
            "show": true,
            "scale": 0.5
        },
        "react": {
            "opacityDefault": 0.7,
            "opacityOnHover": 0.2
        }
    });
</script>

</body>
</html>
