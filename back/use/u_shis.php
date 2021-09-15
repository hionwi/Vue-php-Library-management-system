<?php
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$ro = file_get_contents('php://input');  //得到前端的json文件
$user = json_decode($ro, true, JSON_UNESCAPED_UNICODE);    //转变成php数组
$u_ID = $user['u_ID'];
$ISBN=$user['ISBN'];
$b_name=$user['b_name'];
//$u_ID='201900130098';
//$ISBN='%';
//$b_name='围城';
$conn = new mysqli('localhost', 'root', '1234qwer!@#$', 'bianquan');

//$conn=new mysqli('42.192.40.242','test','1234QWer!@#$','bianquan');
//判断连接是否成功
if ($conn->connect_error) {
  die("连接失败" . $conn->connect_error);
}
mysqli_set_charset($conn, 'utf8');
$conn->query('SET NAMES UTF8');

$data = array();
class u_b
{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $start_time;
  public $rule_time;
  public $true_time;
  public $fail_status;
  public $deal_status;
  public $status;
}

if($ISBN!='%'){
  $sql5="select ISBN,b_name,author,type_name
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";
  $result5=$conn->query($sql5);
  $re=mysqli_num_rows($result5);
  if($re==0)
  {
    $row['status']='0';
    //echo (json_encode($row));
    array_push($data,$row);
    mysqli_close($conn);
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    exit;
  }
  $row5=mysqli_fetch_array($result5,MYSQLI_ASSOC);
  mysqli_free_result($result5);
  $sql="select * from log where ISBN=$ISBN and u_ID='$u_ID'";
  $result=$conn->query($sql);
  $r=mysqli_num_rows($result);
  if($r==0)
  {
    $row['status']=0;
    array_push($data,$row);
    mysqli_close($conn);
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    exit;
  }
  while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
  {
    $u=new u_b();
    $u->ISBN=$row5['ISBN'];
    $u->b_name=$row5['b_name'];
    $u->author=$row5['author'];
    $u->type_name=$row5['type_name'];
    $u->start_time=$row['start_time'];
    $u->rule_time=$row['rule_time'];
    $u->true_time=$row['true_time'];
    $u->fail_status=$row['fail_status'];
    $u->deal_status=$row['deal_status'];
    $u->status='1';
    array_push($data,$u);
  }
  mysqli_close($conn);
  echo json_encode($data,JSON_UNESCAPED_UNICODE);
  exit;
}

$sql2="select ISBN from books where b_name='$b_name'";
$result2=$conn->query($sql2);
$q=mysqli_num_rows($result2);
if($q==0)
{
  $row['status']='0';
  //echo (json_encode($row));
  array_push($data,$row);
  mysqli_close($conn);
  echo json_encode($data,JSON_UNESCAPED_UNICODE);
  exit;
}
while($row2=mysqli_fetch_array($result2,MYSQLI_ASSOC))
{
  $ISBN=$row2['ISBN'];
  $sql5="select ISBN,b_name,author,type_name
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";
  $result5=$conn->query($sql5);
  $re=mysqli_num_rows($result5);
  if($re==0)
  {
    $row['status']='0';
    //echo (json_encode($row));
    array_push($data,$row);
    mysqli_close($conn);
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    exit;
  }
  $row5=mysqli_fetch_array($result5,MYSQLI_ASSOC);
  mysqli_free_result($result5);
  $sql="select * from log where ISBN=$ISBN and u_ID='$u_ID'";
  $result=$conn->query($sql);
  $r=mysqli_num_rows($result);
  if($r==0)
  {
    $row['status']=0;
    array_push($data,$row);
    mysqli_close($conn);
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    exit;
  }
  while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
  {
    $u=new u_b();
    $u->ISBN=$row5['ISBN'];
    $u->b_name=$row5['b_name'];
    $u->author=$row5['author'];
    $u->type_name=$row5['type_name'];
    $u->start_time=$row['start_time'];
    $u->rule_time=$row['rule_time'];
    $u->true_time=$row['true_time'];
    $u->fail_status=$row['fail_status'];
    $u->deal_status=$row['deal_status'];
    $u->status='1';
    array_push($data,$u);
  }
  mysqli_close($conn);
  echo json_encode($data,JSON_UNESCAPED_UNICODE);
  exit;
}
