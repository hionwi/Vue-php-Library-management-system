<?php
//********************//
//文件名：d_book.php //
//功  能：删除对应书籍信息 //
//功能发出对象：管理员     //
//作  者：冯智洋        //
//时  间：2020/11/22  //
//版  本：1.0.0        //


//设置请求头完成跨域访问
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");


$ro = file_get_contents('php://input');  //得到前端的json文件
$book = json_decode($ro, true);    //转变成php数组
$ISBN=$book['ISBN'];  //获取对应信息并存入变量
//$ISBN='1234567890123';

//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');

//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}

//连接成功设置编码格式，避免中文乱码
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

//执行SQL语句，删除对应书籍信息
$sql="delete from books where ISBN=$ISBN";
$conn->query($sql);

//判断SQL语句执行的影响行数
$result=mysqli_affected_rows($conn);
if($result==0)
{//删除未成功
  $row['status']='0';
  echo (json_encode($row));
  mysqli_close($conn);
  exit;
}
//删除成功
//执行SQL语句找出该书的借阅记录
//强制执行还书及判断违规的操作
$sql9="select u_ID from user_books where fail_status='0' and (now()>rule_time) and readed='0'";
$result3=$conn->query($sql9);
$row2=mysqli_num_rows($result3);
if($row2!=0)
{
  while($ro=mysqli_fetch_row($result3,MYSQLI_ASSOC))
  {
    $k=$ro['u_ID'];
    $sql10="update user_books set readed='1',fail_status='1' where u_ID='$k'";
    $conn->query($sql10);
    $sql11="update user set u_fail=u_fail+1 where u_ID='$k'";
    $conn->query($sql11);
  }
}

$sql1="select count(*) from user_books where ISBN=$ISBN";
$result1=$conn->query($sql1);
$re=mysqli_fetch_array($result1,MYSQLI_ASSOC);
if($re['count(*)']!=0)
{//查询到有借阅记录，即当前未在馆
  //执行SQL语句删除存在信息，同时用户借书数量减少
  $sql4="select u_ID,fail_status from user_books where ISBN=$ISBN";
  $r=$conn->query($sql4);
  $res=mysqli_fetch_array($r,MYSQLI_ASSOC);
  $t=$res['u_ID'];
  mysqli_free_result($r);
  $sql2="delete from user_books where ISBN=$ISBN";
  $conn->query($sql2);
  $sql3="update user set u_number=u_number-1 where u_ID='$t'";
  $conn->query($sql3);
  if($res['fail_status']==1)
  {
    $sql5="update user set u_fail=ufail-1 where u_ID='$t'";
  }
  //强制处理本书历史违规
  $sql6="select u_ID from log where ISBN=$ISBN and fail_status='1' and deal_status='0'";
  $result2=$conn->query($sql6);
  while($row1=mysqli_fetch_array($result2,MYSQLI_ASSOC)){
    $u_ID=$row1['u_ID'];
    $sql7="update user set u_fail=u_fail-1 where u_ID='$u_ID'";
    $conn->query($sql7);
  }
  //将该书信息删除
  $sql8="delete from log where ISBN=$ISBN";
  $conn->query($sql8);
}

//删除信息已成功，返回前端状态码
$row['status']='1';
echo (json_encode($row));
mysqli_close($conn);

