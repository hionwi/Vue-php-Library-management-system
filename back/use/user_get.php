<?php
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$ro = file_get_contents('php://input');  //得到前端的json文件
$u_book=json_decode($ro, true,JSON_UNESCAPED_UNICODE);    //转变成php数组
$ISBN=$u_book['ISBN'];
$u_ID=$u_book['u_ID'];
//$ISBN='9787559639301';
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


$sql="select u_fail,u_number from user where u_ID='$u_ID'";
$sql4="select status from books where ISBN=$ISBN";
$result=$conn->query($sql);
$result1=$conn->query($sql4);
$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
$row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
mysqli_fetch_array($result);
if($row['u_fail']>5||$row1['status']==0)
{
  $row2['status']='0';
  //echo (json_encode($row));
  mysqli_close($conn);
  echo json_encode($row2,JSON_UNESCAPED_UNICODE);
  exit;
}

if($row['u_number']==10)
{
  $row2['status']='-1';
  //echo (json_encode($row));
  mysqli_close($conn);
  echo json_encode($row2,JSON_UNESCAPED_UNICODE);
  exit;
}


$sql1="insert into user_books values($ISBN,'$u_ID',now(),date_add(now(),interval 30 day),'0','0','0')";
$conn->query($sql1);
$re=mysqli_affected_rows($conn);
if($re==0){
  $row2['status']='0';
  //echo (json_encode($row));
  mysqli_close($conn);
  echo json_encode($row2,JSON_UNESCAPED_UNICODE);
  exit;
}

$sql2="update user set u_number=u_number+1 where u_ID='$u_ID'";
$conn->query($sql2);

$sql3="update books set status='0',b_number=b_number+1 where ISBN=$ISBN";
$conn->query($sql3);

$row2['status']='1';
mysqli_close($conn);
echo json_encode($row2,JSON_UNESCAPED_UNICODE);
