<?php

//********************//
//文件名：u_ad.php //
//功  能：更改密码 //
//功能发出对象：管理员     //
//作  者：冯智洋        //
//时  间：2020/11/22  //
//版  本：1.0.0        //


header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$ro = file_get_contents('php://input');  //得到前端的json文件
$ad = json_decode($ro, true);    //转变成php数组


$a_ID=$ad['a_ID'];//获取对应信息
$a_old_pwd=$ad['a_old_pwd'];
$a_new_pwd=$ad['a_new_pwd'];
//$a_ID='1009';
//$a_old_pwd='5678';
//$a_new_pwd='1008';


//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
//设置编码格式，避免中文乱码
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

//旧密码是否匹配
$sql="select * from ad where (a_ID='$a_ID') and (a_pwd='$a_old_pwd')";
$result=$conn->query($sql);
$re=mysqli_num_rows($result);
mysqli_free_result($result);
if($re==0)
{
  $row['status']='0';
  echo (json_encode($row));
  mysqli_close($conn);
  exit;
}
//新密码更改
$sql1="update ad set a_pwd='$a_new_pwd' where a_ID='$a_ID'";
$conn->query($sql1);
$row['status']='1';
echo (json_encode($row));
mysqli_close($conn);

