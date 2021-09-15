<?php
//********************//
//文件名：s_alluser.php //
//功  能：查询所有用户信息 //
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

//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
$data=array();
//设置数据库编码，避免中文乱码的出现
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

class User  //新建USER信息类
{
  public $u_ID;
  public $u_name;
  public $u_number;
  public $u_fail;
}

//从用户表单中找出所有用户的ID
$sql="select u_ID from user";
$result=$conn->query($sql);


//结果放入关联数组中进行循环
while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
{ //进行用户违规自动化处理，如果当前借阅中存在未读且超时的信息，则用户违规次数进行更新后呈现
  $u_ID=$row['u_ID'];
  $sql1="select count(ISBN) from user_books where u_ID='$u_ID' and (now()>rule_time) and readed='0'";
  $result1=$conn->query($sql1);
  $row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
  mysqli_free_result($result1);


  if($row1['count(ISBN)']==0)
  {//如果当前借阅中不存在未读且超时的信息
    $sql2="select * from user where u_ID=$u_ID";
    $result2=$conn->query($sql2);
    $row2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
    mysqli_free_result($result2);
    $user=new User();
    $user->u_ID=$row2['u_ID'];
    $user->u_name=$row2['u_name'];
    $user->u_number=$row2['u_number'];
    $user->u_fail=$row2['u_fail'];
    array_push($data,$user);
  }


  else{//如果当前借阅中存在未读且超时的信息
    $sql3="select u_fail from user where u_ID='$u_ID'";
    $result3=$conn->query($sql3);
    $row3=mysqli_fetch_array($result3,MYSQLI_ASSOC);
    mysqli_free_result($result3);
    $temp=$row3['u_fail']+$row1['count(ISBN)'];
    $sql4="update user set u_fail='$temp'";
    $conn->query($sql4);
    $sql6="select ISBN from user_books where (u_ID='$u_ID') and (readed='0') and (now()>rule_time)";
    $result5=$conn->query($sql6);
    while($row5=mysqli_fetch_array($result5)){
      $t_ISBN=$row5['ISBN'];
      $sql7="update user_books set readed='1',fail_status='1' where ISBN='$t_ISBN'";
      $conn->query($sql7);
    }
    mysqli_free_result($result5);
    $sql5="select * from user where u_ID=$u_ID";
    $result4=$conn->query($sql5);
    $row4=mysqli_fetch_array($result4,MYSQLI_ASSOC);
    mysqli_free_result($result4);
    $user=new User();
    $user->u_ID=$row4['u_ID'];
    $user->u_name=$row4['u_name'];
    $user->u_number=$row4['u_number'];
    $user->u_fail=$row4['u_fail'];
    array_push($data,$user);
  }
}

mysqli_free_result($result);
$conn->close();
echo json_encode($data,JSON_UNESCAPED_UNICODE);
