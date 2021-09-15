<?php
//满足跨域访问需求，规定编码格式
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");

$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//$conn=new mysqli('42.192.40.242','test','1234QWer!@#$','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
$data=array();
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

class U_nB
{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $u_ID;
  public $u_name;
  public $start_time;
  public $rule_time;
  public $fail_status;
  public $status;
}

$sql="select ISBN,u_ID from user_books where (now()>rule_time) and readed='0'";
$result=$conn->query($sql);
$re=mysqli_num_rows($result);
if($re!=0)
{
  while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
  {
    $ISBN=$row['ISBN'];
    $u_ID=$row['u_ID'];
    $sql1="update user_books set readed='0',fail_status='1' where ISBN=$ISBN";
    $conn->query($sql1);
    $sql2="update user set u_fail=u_fail+1 where u_ID='$u_ID'";
    $conn->query($sql2);
  }
}

$sql3="select * from user_books where fail_status='1'";
$result1=$conn->query($sql3);
$r=mysqli_num_rows($result1);
if($r==0)
{
  $row1['status']='0';
  array_push($data,$row1);
  mysqli_free_result($result1);
  $conn->close();
  echo json_encode($data,JSON_UNESCAPED_UNICODE);
  exit;
}

while($row2=mysqli_fetch_array($result1,MYSQLI_ASSOC))
{
  $ISBN=$row2['ISBN'];
  $u_ID=$row2['u_ID'];
  $sql4="select ISBN,b_name,author,type_name
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";
  $result2=$conn->query($sql4);
  $row3=mysqli_fetch_array($result2,MYSQLI_ASSOC);
  $sql5="select u_ID,u_name from user where u_ID='$u_ID'";
  $result3=$conn->query($sql5);
  $row4=mysqli_fetch_array($result3,MYSQLI_ASSOC);
  $ubn=new U_nB();
  $ubn->ISBN=$row3['ISBN'];
  $ubn->b_name=$row3['b_name'];
  $ubn->author=$row3['author'];
  $ubn->type_name=$row3['type_name'];
  $ubn->u_ID=$row4['u_ID'];
  $ubn->u_name=$row4['u_name'];
  $ubn->start_time=$row2['start_time'];
  $ubn->rule_time=$row2['rule_time'];
  $ubn->fail_status=$row2['fail_status'];
  $ubn->status='1';
  array_push($data,$ubn);
}
mysqli_free_result($result1);
$conn->close();
echo json_encode($data,JSON_UNESCAPED_UNICODE);
