<?php
//满足跨域访问需求，规定编码格式
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$ro = file_get_contents('php://input');  //得到前端的json文件
$u_book=json_decode($ro, true,JSON_UNESCAPED_UNICODE);    //转变成php数组
$ISBN=$u_book['ISBN'];
$u_ID=$u_book['u_ID'];
//$ISBN='9787020031924';
//$u_ID='201900130098';
//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//$conn=new mysqli('42.192.40.242','test','1234qwerasdf','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

$sql6="select u_fail from user where u_ID='$u_ID'";
$result3=$conn->query($sql6);
$ro=mysqli_fetch_array($result3,MYSQLI_ASSOC);
mysqli_free_result($result3);
if($ro['u_fail']>5)
{
  $conn->close();
  $row['status']='0';
  echo json_encode($row,JSON_UNESCAPED_UNICODE);
  exit;
}
$sql7="select retimes from user_books where ISBN=$ISBN";
$result4=$conn->query($sql7);
$row5=mysqli_fetch_array($result4,MYSQLI_ASSOC);
mysqli_free_result($result4);
if($row5['retimes']==2)
{
  $conn->close();
  $row['status']='2';
  echo json_encode($row,JSON_UNESCAPED_UNICODE);
  exit;
}


$sql="select count(ISBN) from user_books where ISBN=$ISBN and (now()>rule_time)";
$result=$conn->query($sql);
$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
mysqli_free_result($result);
if($row['count(ISBN)']==0)
{
  $sql1="update user_books set rule_time=date_add(rule_time,interval 30 day),readed='0',retimes=retimes+1 where ISBN=$ISBN";
  $conn->query($sql1);
  $conn->close();
  $row4['status']='1';
  echo json_encode($row4,JSON_UNESCAPED_UNICODE);
  exit;
}

$sql2="select count(ISBN) from user_books where ISBN=$ISBN and (now()>rule_time) and readed='0'";
$result1=$conn->query($sql2);
$row2=mysqli_fetch_array($result1,MYSQLI_ASSOC);
mysqli_free_result($result1);
if($row2['count(ISBN)']>0){
  $sql3="update user_books set rule_time=date_add(rule_time,interval 30 day),readed='0',fail_status='1',retimes=retimes+1 where ISBN=$ISBN";
  $sql4="update user set u_fail=u_fail+1 where u_ID='$u_ID'";
  $conn->query($sql3);
  $conn->query($sql4);
  $conn->close();
  $row4['status']='-1';
  echo json_encode($row4,JSON_UNESCAPED_UNICODE);
  exit;
}

$sql5="select count(ISBN) from user_books where ISBN=$ISBN and (now()>rule_time) and readed='1'";
$result2=$conn->query($sql5);
$row3=mysqli_fetch_array($result2,MYSQLI_ASSOC);
mysqli_free_result($result2);
if($row3['count(ISBN)']>0){
  $sql3="update user_books set rule_time=date_add(rule_time,interval 30 day),readed='0',fail_status='1',retimes=retimes+1 where ISBN=$ISBN";
  $conn->query($sql3);
  $conn->close();
  $row4['status']='-1';
  echo json_encode($row4,JSON_UNESCAPED_UNICODE);
  exit;
}
