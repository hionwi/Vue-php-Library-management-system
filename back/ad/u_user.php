<?php


//********************//
//文件名：u_user.php //
//功  能：更改用户信息，重置违规 //
//功能发出对象：管理员     //
//作  者：冯智洋        //
//时  间：2020/11/22  //
//版  本：1.0.0        //

//满足跨域访问需求，规定编码格式
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");

$ro=file_get_contents('php://input');  //得到前端的json文件
$user=json_decode($ro,true);    //转变成php数组
$u_ID=$user['u_ID'];     //取出对应数据
//$u_ID='1001';
//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');

//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
//设置编码格式，避免乱码
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

$sql="select * from user where (u_ID='$u_ID')";
$result=$conn->query($sql);
$re=mysqli_num_rows($result);
mysqli_free_result($result);
if($re==0)
{
  $row['status']="0";
  echo (json_encode($row));
  mysqli_close($conn);
  exit;
}

//违规次数自动化
$sql6="select count(*) from user_books where (now()>rule_time) and readed='0' and u_ID='$u_ID'";
$result3=$conn->query($sql6);
$w=mysqli_fetch_array($result3,MYSQLI_ASSOC);
if($w['count(*)']!=0)
{
  $s=$w['count(*)'];
  $sql7="update user set u_fail=u_fail+$s where u_ID='$u_ID'";
  $conn->query($sql7);
}

$sql4="update user_books set fail_status='1',readed='1' where (now()>rule_time) and u_ID='$u_ID'";
$conn->query($sql4);
$sql1="select count(ISBN) from user_books where fail_status='1' and u_ID='$u_ID'";
$result1=$conn->query($sql1);
$re1=mysqli_fetch_array($result1,MYSQLI_ASSOC);

$n=$re1['count(ISBN)'];
mysqli_free_result($result1);
$sql2="update user set u_fail='$n' where u_ID='$u_ID'";
$conn->query($sql2);

//更改处理状态
$sql3="select ISBN from log where u_ID='$u_ID' and fail_status='1' and deal_status='0'";
$result2=$conn->query($sql3);
while($row1=mysqli_fetch_array($result2,MYSQLI_ASSOC)){
  $r=$row1['ISBN'];
  $sql5="update log set deal_status='1' where ISBN='$r'";
  $conn->query($sql5);
}
mysqli_free_result($result2);
$row['status']='1';
echo (json_encode($row));
mysqli_close($conn);
