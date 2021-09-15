<?php
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//$conn=new mysqli('42.192.40.242','test','1234QWer!@#$','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

$data=array();

class u_b
{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $u_ID;
  public $u_name;
  public $start_time;
  public $rule_time;
  public $true_time;
  public $fail_status;
  public $deal_status;
  public $status;
}

$sql="select * from log where fail_status='1'";
$result=$conn->query($sql);
$re=mysqli_num_rows($result);
if($re==0)
{
  $row['status']='0';
  array_push($data,$row);
  mysqli_free_result($result);
  $conn->close();
  echo json_encode($data,JSON_UNESCAPED_UNICODE);
  exit;
}

while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
{
  $ISBN=$row['ISBN'];
  $u_ID=$row['u_ID'];
  $sql2="select ISBN,b_name,author,type_name
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN='$ISBN')";
  $result2=$conn->query($sql2);
  $row2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
  $sql1="select u_ID,u_name from user where u_ID='$u_ID'";
  $result1=$conn->query($sql1);
  $row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
  $u_b=new u_b();
  $u_b->ISBN=$row2['ISBN'];
  $u_b->b_name=$row2['b_name'];
  $u_b->author=$row2['author'];
  $u_b->type_name=$row2['type_name'];
  $u_b->u_ID=$row1['u_ID'];
  $u_b->u_name=$row1['u_name'];
  $u_b->start_time=$row['start_time'];
  $u_b->rule_time=$row['rule_time'];
  $u_b->true_time=$row['true_time'];
  $u_b->fail_status=$row['fail_status'];
  $u_b->deal_status=$row['deal_status'];
  $u_b->status='1';
  array_push($data,$u_b);
}

mysqli_free_result($result);
$conn->close();
echo json_encode($data,JSON_UNESCAPED_UNICODE);

