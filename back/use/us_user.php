<?php
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$ro = file_get_contents('php://input');  //得到前端的json文件
$user=json_decode($ro, true,JSON_UNESCAPED_UNICODE);    //转变成php数组
$u_ID=$user['u_ID'];
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
//$data=array();

$sql1="select u_fail from user where (u_ID='$u_ID')";
$result1=$conn->query($sql1);
$row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
mysqli_free_result($result1);
$sql2="select count(ISBN) from user_books where (u_ID='$u_ID') and (readed='0') and (now()>rule_time)";
$result2=$conn->query($sql2);
$row2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
mysqli_free_result($result2);

if($row2['count(ISBN)']>0)
{
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
class u_User  //新建USER信息类
{
  public $u_ID;
  public $u_name;
  public $u_number;
  public $u_fail;
}
$sql="select u_ID,u_name,u_number,u_fail from user where (u_ID='$u_ID')";
mysqli_set_charset($conn,'utf-8');  //设置编码格式
$result=$conn->query($sql);
$row=mysqli_fetch_array($result,MYSQLI_ASSOC);   //以关联数组的形式将结果放入

$user=new u_User();
$user->u_ID=$row['u_ID'];
$user->u_name=$row['u_name'];
$user->u_number=$row['u_number'];
$user->u_fail=$row['u_fail'];
//array_push($data,$user);
echo (json_encode($user,JSON_UNESCAPED_UNICODE));

mysqli_close($conn);

