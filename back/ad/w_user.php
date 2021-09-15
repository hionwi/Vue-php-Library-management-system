<?php
//满足跨域访问需求，规定编码格式
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");

$ro=file_get_contents('php://input');  //得到前端的json文件
$user=json_decode($ro,true);    //转变成php数组
$u_ID=$user['u_ID'];     //取出对应数据
$u_name=$user['u_name'];
//$u_ID='1003';
//$u_name='王平';
//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');

//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');
$sql="select * from ad where (a_ID='$u_ID')";
$result=$conn->query($sql);
$re=mysqli_num_rows($result);
mysqli_free_result($result);
if($re!=0)
{//判断为管理员信息
  $row['status']="-1";
  echo (json_encode($row));
  mysqli_close($conn);
  exit;
}
else{
  $sql1="select * from user where (u_ID='$u_ID')";
  $result1=$conn->query($sql1);
  $re1=mysqli_num_rows($result1);
  mysqli_free_result($result1);
  if($re1!=0)
  {//判断为用户
    $row['status']='0';
    echo (json_encode($row));
    mysqli_close($conn);
    exit;
  }
}

$sql2="insert into user (u_ID,u_pwd,u_name,u_number,u_fail) values('$u_ID','123456','$u_name','0','0')";
$result3=$conn->query($sql2);
if(!$result3){
  $row['status']='0';
  echo (json_encode($row));
  mysqli_close($conn);
  exit;
}
else{
  $row['status']='1';
  echo (json_encode($row));
  mysqli_close($conn);
}
