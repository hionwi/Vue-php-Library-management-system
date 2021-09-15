<?php
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
  $row1['status']='0';
  //echo (json_encode($row));
  mysqli_close($conn);
  echo json_encode($row1,JSON_UNESCAPED_UNICODE);
  exit;
}
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

$sql="select count(ISBN) from user_books where ISBN=$ISBN and (now()>rule_time) and (readed='1')";
$sql4="select count(ISBN) from user_books where ISBN=$ISBN and (now()<=rule_time)";
$result=$conn->query($sql);
$result1=$conn->query($sql4);
$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
$row2=mysqli_fetch_array($result1,MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_free_result($result1);
if($row['count(ISBN)']>0||$row2['count(ISBN)']>0){
  $sql6="select fail_status,start_time,rule_time from user_books where ISBN=$ISBN";
  $result3=$conn->query($sql6);
  $row4=mysqli_fetch_array($result3,MYSQLI_ASSOC);
  mysqli_free_result($result3);
  $fs=$row4['fail_status'];
  $st=$row4['start_time'];
  $rt=$row4['rule_time'];
  $sql7="insert into log values('$ISBN','$u_ID','$st','$rt',now(),'0','0')";
  $conn->query($sql7);


  $sql1="delete from user_books where ISBN=$ISBN";
  $conn->query($sql1);
  $sql2="update user set u_number=u_number-1 where u_ID='$u_ID'";
  $conn->query($sql2);
  $sql3="update books set status='1' where ISBN=$ISBN";
  $conn->query($sql3);
  $row1['status']='1';
  //echo (json_encode($row));
  mysqli_close($conn);
  echo json_encode($row1,JSON_UNESCAPED_UNICODE);
  exit;
}
$sql6="select start_time,rule_time from user_books where ISBN=$ISBN";
$result3=$conn->query($sql6);
$row4=mysqli_fetch_array($result3,MYSQLI_ASSOC);
mysqli_free_result($result3);
$st=$row4['start_time'];
$rt=$row4['rule_time'];
$sql7="insert into log values('$ISBN','$u_ID','$st','$rt',now(),'1','0')";
$conn->query($sql7);
$sql1="delete from user_books where ISBN=$ISBN";
$conn->query($sql1);
$sql2="update user set u_number=u_number-1,u_fail=u_fail+1 where u_ID='$u_ID'";
$conn->query($sql2);
$sql3="update books set status='1' where ISBN=$ISBN";
$conn->query($sql3);
$row1['status']='-1';
  //echo (json_encode($row));
mysqli_close($conn);
echo json_encode($row1,JSON_UNESCAPED_UNICODE);


