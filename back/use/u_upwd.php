<?php
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$ro = file_get_contents('php://input');  //得到前端的json文件
$user = json_decode($ro, true);    //转变成php数组
$u_ID=$user['u_ID'];
$u_old_pwd=$user['u_old_pwd'];
$u_new_pwd=$user['u_new_pwd'];
//$u_ID='1001';
//$u_old_pwd='1002';
//$u_new_pwd='1003';


//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');
$sql="select * from user where (u_ID='$u_ID') and (u_pwd='$u_old_pwd')";
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

$sql1="update user set u_pwd='$u_new_pwd' where u_ID='$u_ID'";
$conn->query($sql1);
$row['status']='1';
echo (json_encode($row));
mysqli_close($conn);

