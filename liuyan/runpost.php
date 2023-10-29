<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
</head>

<body>


<?php


require_once('set.php');
$word_max=160000; //留言最多字数，最多500



//填写
if($_REQUEST['act']=='write'):
  /*
  if(!file_exists('data')) mkdir('data');
  if(!file_exists('data/file.php')) touch_f('data/file.php','<?php die(); ?>');
  if(!file_exists('data/index.txt')) touch_f('data/index.txt','');
  if(!file_exists('data/space.txt')) touch_f('data/space.txt','');
  */
  $posttype=$_POST['posttype'];
  if(preg_match('/<([a-z]+)[^<>]*>.*<\/\1>/isU',$_POST['content'],$matches)){
    die('<script>alert("留言本不支持此代码'.$matches[0].'！提交无用");</script></body></html>');
  }
  $contents=filter($_POST['content']);
  if($posttype!=1 && $posttype!=2 && $posttype!=3 && $posttype!=4)
    $posttype=1;
  if($contents=='' || strlen($contents)>$word_max){
    die('<script>alert("留言内容不能空且不能超过'.$word_max.'字符！");</script></body></html>');
  }
  if(isset($_COOKIE['regimcode'])){
    if($_POST['imcode']!=$_COOKIE['regimcode'])
      die('<script>alert("验证码不符！");</script></body></html>');
  }
  $str=date('Y/m/d H:i').'|'.$posttype.'|'.$contents.'|';
  $strlen=strlen($str);
  $loc=find_space($strlen);
  //处理索引文件
  if(is_writable('data/index.txt') && ($fp=fopen('data/index.txt','ab'))){
    f_lock($fp);
    @fwrite($fp,'.'.str_pad($loc.'|'.$strlen,12));
    @fclose($fp);
  }else{
    die('<script>alert("目录权限不足！请参阅安装文件（read.txt）");</script></body></html>');
  }
  //处理数据文件
  if(is_writable('data/file.php') && ($fp=@fopen('data/file.php','rb+'))){
    @fseek($fp,$loc);
    f_lock($fp);
    @fwrite($fp,$str);
    @fclose($fp);
  }else{
    die('<script>alert("目录权限不足！请参阅安装文件（read.txt）");</script></body></html>');
  }
  die('<script>

alert("留言完成！");
parent.document.getElementById("imcode").value="";
parent.document.getElementById("imFrame").src="js/imcode.html";
parent.location.href="contact_book.php";

</script></body></html>');




//删除
elseif($_REQUEST['act']=='del'):
  if($_COOKIE['manage']!=1){
    die('<script>alert("管理权限不足！");</script></body></html>');
  }
  $_REQUEST['id']=array_unique(array_filter((array)$_REQUEST['id']));
  if(!$_REQUEST['id']){
    die('<script>alert("参数缺失！或你未选择文件");</script></body></html>');
  }
  if(!$content_index=@file_get_contents('data/index.txt')){
    die('<script>alert("索引数据库打不开，或无数据！");</script></body></html>');
  }
  foreach($_REQUEST['id'] as $each){
    if(preg_match('/\.+'.$each.'\|(\d+)\s*/',$content_index,$matches)){
      $len=$matches[1];
    }
    if($len){
	  $content_index=preg_replace('/\.+'.$each.'\|'.$len.'\s*/','',$content_index);
      //处理索引文件
      write_f('data/index.txt',$content_index);
      //处理数据文件（可以不管它）
      //处理空间文件
      run_space($each,$len);
	}
	unset($len,$matches,$each);
  }
  die('<script>alert("删除完成！");parent.location.reload();</script></body></html>');


      

//回复
elseif($_REQUEST['act']=='reply'):
  if($_COOKIE['manage']!=1){
    die('<script>alert("管理权限不足！");</script></body></html>');
  }
  if(!$_REQUEST['id'] || !is_numeric($_REQUEST['id'])){
    die('<script>alert("参数缺失！");</script></body></html>');
  }
  if($content_index=@file_get_contents('data/index.txt')){
    if(preg_match('/\.+'.$_REQUEST['id'].'\|(\d+)\s*/',$content_index,$matches)){
      $len=$matches[1];
    }
  }
  if(!$len){
    die('<script>alert("参数错误！或获取索引文件失败");</script></body></html>');
  }
  $contents=filter($_REQUEST['content']);
  if($contents==''){
    die('<script>alert("回复内容不能空！");</script></body></html>');
  }
  $num=strlen($contents);
  if(999-$len>0){
    if($num-(999-$len)>0){
      die('<script>alert("你最多只能再写'.(999-$len).'字符！请尝试减少'.($num-(999-$len)).'字符");</script></body></html>');
    }
  }else{
    die('<script>alert("回复容量已满！不要再回复");</script></body></html>');
  }
  //处理空间文件
  run_space($_REQUEST['id'],$len);
  //处理数据文件
  if(is_writable('data/file.php') && ($fp=@fopen('data/file.php','rb+'))){
    @fseek($fp,$_REQUEST['id']);
    $line=@fread($fp,$len);
    $str=$line.$contents;
    $strlen=strlen($str);
    $loc=find_space($strlen);
    //处理数据文件
    @fseek($fp,$loc);
    f_lock($fp);
    @fwrite($fp,$str);
    @fclose($fp);
  }else{
    die('<script>alert("目录权限不足！请参阅安装文件（read.txt）");</script></body></html>');
  }
  //处理索引文件
  write_f('data/index.txt',preg_replace('/\.+'.$_REQUEST['id'].'\|'.$len.'\s*/','.'.str_pad($loc.'|'.$strlen,12).'',$content_index));
  die('<script>alert("回复完成！");parent.location.reload();</script></body></html>');




//登录
elseif($_REQUEST['act']=='login'):
  if($_REQUEST['password']=='')
    die('<script>alert("密码不能空！");</script></body></html>');
  if($_COOKIE['manage']==1)
    die('<script>alert("您已经登录过了！");</script></body></html>');

  if($_REQUEST['password']==$web['password']){
    //用js设置cookie，因为前面已有输出（本程序采用cookie验证登录，当然最好使用session，并不复杂）
    echo '
      <script language="javascript" type="text/javascript">
      <!--
      document.cookie="manage=1;";
      -->
      </script>';

    die('<script>alert("登录成功！欢迎您");parent.location.href="contact_book.php";</script></body></html>');
  }else{
    die('<script>alert("你输入的密码不符！");</script></body></html>');
  }




//退出
elseif($_REQUEST['act']=='logout'):
  //用js设置cookie，因为前面已有输出
  echo '
      <script language="javascript" type="text/javascript">
      <!--
      document.cookie="manage=;";
      -->
      </script>';
  die('<script>parent.location.href="contact_book.php";</script></body></html>');




else:
  die('<script>alert("错误的命令！");</script></body></html>');




endif;







//过滤提交
function filter($text){
  $text=trim($text);
  $text=stripslashes($text);
  $text=htmlspecialchars($text);
  $text=str_replace('  ',' ',$text);
  $text=preg_replace('/[\r\n]+/','',$text);
  return $text;
}

//写文件
function write_f($file,$text){
  if(!file_exists($file)){
    if(!@touch($file)){
      die('<script>alert("操作失败！原因分析：1、文件'.$file.'无法创建。请稍后再试 2、目录权限不足！请参阅安装文件（read.txt）");</script>');
    }
  }
  @chmod($file,0777);
  if(is_writable($file) && ($fp=@fopen($file,'rb+'))){
    f_lock($fp);
    @ftruncate($fp,0);
    @fwrite($fp,$text);
    @flock($fp,LOCK_UN);
    fclose($fp);
  }else{
    die('<script>alert("目录权限不足！请参阅安装文件（read.txt）");</script></body></html>');
  }
}

//锁定文件
function f_lock($fp){
  if($fp){
    if(!flock($fp,LOCK_EX)){
      sleep(1);
      f_lock($fp);
    }
  }
}

//创建数据库文件
function touch_f($f,$fl){
  if(is_writable($f) && ($fp=@fopen($f,'wb+'))){
    @fwrite($fp,$fl);
    @fclose($fp);
  }else{
    die('<script>alert("目录权限不足！请参阅安装文件（read.txt）");</script></body></html>');
  }
}

//找可用的空闲空间
function find_space($strlen){
  global $space_list;
  $max_str=22; //0000/00/00 00:00|X|XXX              这是数据最小长度，具体根据你的设计结构而变
  $loc=0;
  $space_list=$space_list?$space_list:@file('data/space.txt');
  if($space_list){
    foreach($space_list as $id=>$line){
      $rows=@explode('|',trim($line));
      if($rows[1]==$strlen){
        $loc=$rows[0];
        unset($space_list[$id]);
        $seek_arr=array();
        break;
      }
      if($rows[1]>$strlen){
        $seek_arr[$id]=$rows[1]-$strlen;
      }
    }
    if(isset($seek_arr)){
      if(count($seek_arr)>0){
        asort($seek_arr);
        $min=reset($seek_arr);
        $key=key($seek_arr);
        $loc=abs($space_list[$key]);
        if($min<$max_str){
          unset($space_list[$key]);
        }else{
          $space_list[$key]=($loc+$strlen).'|'.$min.'
';
        }
      }
      write_f('data/space.txt',@implode('',$space_list));
    }
  }
  if($loc==0){
    $loc=filesize('data/file.php');
    if($loc+$strlen>99999999){
      die('<script>alert("数据库已超过容量限制！设计容量为99999999B！请更新数据库或删除记录腾出空间");</script></body></html>');
    }
  }
  //unset($space_list,$id,$line,$rows,$seek_arr,$min,$key);

  return $loc;
}

//优化处理空闲空间
function run_space($loc0,$len0){
  global $space_list;
  if($space_list=@file('data/space.txt')){
    foreach($space_list as $id=>$line){
      $rows=@explode('|',trim($line));
      if($rows[0]+$rows[1]==$loc0){
        unset($space_list[$id]);
        $mark1=1;
        $loc1=$rows[0];
        $len1=$rows[1];
      }
      if($loc0+$len0==$rows[0]){
        unset($space_list[$id]);
        $mark2=1;
        $loc2=$rows[0];
        $len2=$rows[1];
      }
      if($mark1==1 && $mark2==1){
        break;
      }
    }
  }
  if($mark1==1 && $mark2==1){
    $loc=$loc1;
    $len=$len1+$len0+$len2;
  }elseif($mark1==1){
    $loc=$loc1;
    $len=$len1+$len0;
  }elseif($mark2==1){
    $loc=$loc0;
    $len=$len0+$len2;
  }else{
    $loc=$loc0;
    $len=$len0;
  }
  $space_list[]=$loc.'|'.$len.'
';
  write_f('data/space.txt',@implode('',$space_list));
}

?>
</body>
</html>
