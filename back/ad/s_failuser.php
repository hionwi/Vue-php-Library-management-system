<?php

//********************//
//文件名：s_failuser.php //
//功  能：查询所有违规用户信息 //
//功能发出对象：管理员     //
//作  者：冯智洋        //
//时  间：2020/11/22  //
//版  本：1.0.0        //

//满足跨域访问需求，规定编码格式
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");


//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//$conn=new mysqli('42.192.40.242','test','1234qwerasdf','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}


$data=array();
//设置编码格式，避免中文乱码
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

class User  //新建USER信息类
{
  public $u_ID;
  public $u_name;
  public $u_number;
  public $u_fail;
  public $status;
}


//违规自动化更新处理
//如果当前借阅中，对应用户存在未读超时信息，则对违规次数进行更新
$sql="select ISBN,u_ID from user_books where (now()>rule_time) and readed='0'";
$result=$conn->query($sql);
$re=mysqli_num_rows($result);
if($re!=0)
{
  while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
  {
    $ISBN=$row['ISBN'];
    $u_ID=$row['u_ID'];
    $sql1="update user_books set readed='1',fail_status='1' where ISBN=$ISBN";
    $conn->query($sql1);
    $sql2="update user set u_fail=u_fail+1 where u_ID='$u_ID'";
    $conn->query($sql2);
  }
}
mysqli_free_result($result);
//查询违规用户的所有信息
$sql3="select * from user where u_fail>0";
$result1=$conn->query($sql3);
$r=mysqli_num_rows($result1);
if($r==0)
{
  $row1['status']='0';
  array_push($data,$row1);
  mysqli_free_result($result1);
  $conn->close();
  echo json_encode($data,JSON_UNESCAPED_UNICODE);  //JSON数组返回前端
  exit;
}

while($row2=mysqli_fetch_array($result1,MYSQLI_ASSOC))
{
  $us=new User();
  $us->u_ID=$row2['u_ID'];
  $us->u_name=$row2['u_name'];
  $us->u_number=$row2['u_number'];
  $us->u_fail=$row2['u_fail'];
  $us->status='1';
  array_push($data,$us);
}
mysqli_free_result($result1);
$conn->close();
echo json_encode($data,JSON_UNESCAPED_UNICODE);//JSON数组返回前端

