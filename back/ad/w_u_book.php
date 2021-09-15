<?php
//满足跨域访问需求，规定编码格式
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$ro = file_get_contents('php://input');  //得到前端的json文件
$book = json_decode($ro, true,JSON_UNESCAPED_UNICODE);    //转变成php数组
$ISBN=$book['ISBN'];
$b_name=$book['b_name'];
$author=$book['author'];
$type_name=$book['type_name'];
//$ISBN='1234567891235';
//$b_name='白鹿原';
//$author='陈忠实';
//$type_name='小说';

//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');
$sql="select ISBN from books where (ISBN=$ISBN)";
$result=$conn->query($sql);
$re=mysqli_num_rows($result);
if($re!=0)
{//查询到
  $sql1="select type_ID from type_b where type_name='$type_name'";
  $result1=$conn->query($sql1);
  $r=mysqli_num_rows($result1);
  if($r==0)
  {
    $sql2="select type_ID from type_b where (type_ID)=(select max(type_ID) from type_b)";
    $result2=$conn->query($sql2);
    $res=mysqli_fetch_array($result2,MYSQLI_ASSOC);
    $t=$res['type_ID']+1;
    $sql3="insert into type_b values('$t','$type_name')";
    $conn->query($sql3);
    $sql4="update books set b_name='$b_name',author='$author',type_ID='$t' where ISBN=$ISBN";
    $conn->query($sql4);
    mysqli_free_result($result2);
  }
  else{
    $w=mysqli_fetch_array($result1,MYSQLI_ASSOC);
    $t=$w['type_ID'];
    $sql5="update books set b_name='$b_name',author='$author',type_ID='$t' where ISBN=$ISBN";
    $conn->query($sql5);
  }
  mysqli_free_result($result1);
}
else{
  mysqli_free_result($result);
  $sql6="select type_ID from type_b where type_name='$type_name'";
  $result3=$conn->query($sql6);
  $r=mysqli_num_rows($result3);
  if($r==0)
  {
    $sql7="select type_ID from type_b where (type_ID)=(select max(type_ID) from type_b)";
    $result4=$conn->query($sql7);
    $res=mysqli_fetch_array($result4,MYSQLI_ASSOC);
    $t=$res['type_ID']+1;
    $sql8="insert into type_b values('$t','$type_name')";
    $conn->query($sql8);
    $sql9="insert into books values('$ISBN','$b_name','$author','$t','1','0')";
    $conn->query($sql9);
    mysqli_free_result($result4);
  }
  else{
    $w=mysqli_fetch_array($result3,MYSQLI_ASSOC);
    $t=$w['type_ID'];
    $sql10="insert into books values('$ISBN','$b_name','$author','$t','1','0')";
    $conn->query($sql10);
  }
  mysqli_free_result($result3);
}

$row['status']='1';
echo (json_encode($row));
mysqli_close($conn);
