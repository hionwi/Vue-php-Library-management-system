<?php

//********************//
//文件名：s_user.php //
//功  能：查询用户信息 //
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
//$u_ID='201900130098';


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

//连接成功对数据库进行操作
$sql="select u_ID,u_name,u_number,u_fail from user where (u_ID='$u_ID')";
mysqli_set_charset($conn,'utf-8');  //设置编码格式
$result=$conn->query($sql);
$re=mysqli_num_rows($result);
if($re==0)
{//未查询到
  $row['status']='0';
  array_push($data,$row);
  echo json_encode($data,JSON_UNESCAPED_UNICODE);
  mysqli_close($conn);
  exit;
}
class User  //新建USER信息类
{
  public $u_ID;
  public $u_name;
  public $u_number;
  public $u_fail;
  public $status;
}

//违规次数自动化
$sql1="select u_fail from user where (u_ID='$u_ID')";
$result1=$conn->query($sql1);
$row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
mysqli_free_result($result1);
$sql2="select count(ISBN) from user_books where (u_ID='$u_ID') and (readed='0') and (now()>rule_time)";
$result2=$conn->query($sql2);
$row2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
mysqli_free_result($result2);

if($row2['count(ISBN)']>0)
{//违规且未读信息不为0，更新对应信息
  $sql6="select ISBN from user_books where (u_ID='$u_ID') and (readed='0') and (now()>rule_time)";
  $result3=$conn->query($sql6);
  while($row3=mysqli_fetch_array($result3)){
    $t_ISBN=$row3['ISBN'];
    $sql4="update user_books set readed='1',fail_status='1' where ISBN='$t_ISBN'";
    $conn->query($sql4);
  }
  mysqli_free_result($result3);
  $n_fail=$row1['u_fail']+$row2['count(ISBN)'];
  $sql3="update user set u_fail='$n_fail' where u_ID='$u_ID'";
  $conn->query($sql3);
}
$sql5="select u_ID,u_name,u_number,u_fail from user where (u_ID='$u_ID')";
mysqli_set_charset($conn,'utf-8');  //设置编码格式
$result5=$conn->query($sql);
$row=mysqli_fetch_array($result5,MYSQLI_ASSOC);   //以关联数组的形式将结果放入
$user=new User();
$user->status='1';
$user->u_ID=$row['u_ID'];
$user->u_name=$row['u_name'];
$user->u_number=$row['u_number'];
$user->u_fail=$row['u_fail'];

array_push($data,$user);
echo json_encode($data,JSON_UNESCAPED_UNICODE);  //JSON数组返回前端
//echo (json_encode($user,JSON_UNESCAPED_UNICODE));
mysqli_free_result($result);
mysqli_close($conn);



