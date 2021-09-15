<?php
//********************//
//文件名：d_user.php //
//功  能：删除对应用户信息 //
//功能发出对象：管理员     //
//作  者：冯智洋        //
//时  间：2020/11/22  //
//版  本：1.0.0        //



//满足跨域访问需求，规定编码格式
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");

$ro = file_get_contents('php://input');  //得到前端的json文件
$user = json_decode($ro, true);    //转变成php数组
$u_ID = $user['u_ID'];     //取出对应数据
//$u_ID='201900130012';


//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//$conn=new mysqli('42.192.40.242','test','1234qwerasdf','bianquan');  //测试样例

//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}

//连接成功，设置编码格式
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

//删除用户之前强制还书和处理违规
//执行SQL语句找出目标用户的借书数量
$sql="select u_number from user where u_ID='$u_ID'";
$result=$conn->query($sql);

$re=mysqli_num_rows($result);

if($re==0)
{//未查询到该用户
  $row['status']='0';
  echo (json_encode($row));
  $conn->close();
  exit;
}

//将返回结果存入关联数组中
$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
mysqli_free_result($result);
if($row['u_number']==0){
  //当前借书数量为零，则直接删除用户表中的信息
  $sql1="delete from user where u_ID='$u_ID'";
  $conn->query($sql1);
  //删除历史借阅中的信息，代表已经全部处理违规记录
  $sql5="delete from log where u_ID='$u_ID'";
  $conn->query($sql5);
  $row1['status']='1';
  echo (json_encode($row1));
  $conn->close();
}
else{
  //当前借书数量不为零，则删除用户信息同时完成图书的归还
  $sql1="select ISBN from user_books where u_ID='$u_ID'";
  $result1=$conn->query($sql1);
  while($row1=mysqli_fetch_array($result1,MYSQLI_ASSOC)){
    $ISBN=$row1['ISBN'];
    $sql2="delete from user_books where ISBN=$ISBN";
    $conn->query($sql2);
    $sql3="update books set status='1' where ISBN=$ISBN";
    $conn->query($sql3);
  }
  //还书完毕后删除对应用户信息
  $sql4="delete from user where u_ID='$u_ID'";
  $conn->query($sql4);
  $sql5="delete from log where u_ID='$u_ID'";
  $conn->query($sql5);
  $row1['status']='1';
  echo (json_encode($row1));
  $conn->close();
}
