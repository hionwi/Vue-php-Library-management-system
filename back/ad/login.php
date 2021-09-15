<?php
//********************//
//文件名：login.php //
//功  能：登录 //
//功能发出对象：管理员，用户     //
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
$u_pwd=$user['u_pwd'];
//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');

//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
//首先查找管理员中是否有对应信息，然后寻找用户表单
else{//查找管理员表单
  mysqli_set_charset($conn,'utf8');
  $conn->query('SET NAMES UTF8');
  $sql="select * from ad where (a_ID='$u_ID') and (a_pwd='$u_pwd')";
  $result=$conn->query($sql);
  $re=mysqli_num_rows($result);
  mysqli_free_result($result);
  if($re!=0)
  {//判断为管理员
    $row['status']="-1";
  }
  else{//非管理员查找用户表单
    $sql1="select * from user where (u_ID='$u_ID') and (u_pwd='$u_pwd')";
    $result1=$conn->query($sql1);
    $re1=mysqli_num_rows($result1);
    mysqli_free_result($result1);
    if($re1!=0)
    {//判断为用户
      $row['status']='1';
    }
    else{//用户信息不存在
      $row['status']='0';
    }
  }
  echo (json_encode($row));
  mysqli_close($conn);
}


