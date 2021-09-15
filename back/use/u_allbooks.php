<?php
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$ro = file_get_contents('php://input');  //得到前端的json文件
$user=json_decode($ro, true,JSON_UNESCAPED_UNICODE);    //转变成php数组
$u_ID=$user['u_ID'];
//$u_ID='1001';
//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

$data=array();

class U_all_book{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $start_time;
  public $rule_time;
  public $status_b;
  public $fail_status;
}

$sql="select ISBN,start_time,rule_time,fail_status from user_books where (u_ID='$u_ID') and (now()>rule_time)";
$sql1="select ISBN,start_time,rule_time,fail_status from user_books where (u_ID='$u_ID') and (now()<=rule_time)";
$result=$conn->query($sql);
$result1=$conn->query($sql1);
$re=mysqli_num_rows($result);
$re1=mysqli_num_rows($result1);
if($re==0&&$re1==0){
  $row['status_b']='0';
  //echo (json_encode($row));
  array_push($data,$row);
  mysqli_close($conn);
  echo json_encode($data,JSON_UNESCAPED_UNICODE);
  exit;
}
while($row1=mysqli_fetch_array($result,MYSQLI_ASSOC)){
  $ISBN=$row1['ISBN'];
  $sql2="select ISBN,b_name,author,type_name
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";
  $result2=$conn->query($sql2);
  $row2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
  $all=new U_all_book();
  $all->ISBN=$row2['ISBN'];
  $all->b_name=$row2['b_name'];
  $all->author=$row2['author'];
  $all->type_name=$row2['type_name'];
  $all->start_time=$row1['start_time'];
  $all->rule_time=$row1['rule_time'];
  $all->fail_status=$row1['fail_status'];
  $all->status_b='1';
  array_push($data,$all);
  $sql4="update user_books set fail_status='1' where ISBN=$ISBN";
  $conn->query($sql4);
}
while($row3=mysqli_fetch_array($result1,MYSQLI_ASSOC)){
  $ISBN=$row3['ISBN'];
  $sql3="select ISBN,b_name,author,type_name
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";
  $result3=$conn->query($sql3);
  $row4=mysqli_fetch_array($result3,MYSQLI_ASSOC);
  $all=new U_all_book();
  $all->ISBN=$row4['ISBN'];
  $all->b_name=$row4['b_name'];
  $all->author=$row4['author'];
  $all->type_name=$row4['type_name'];
  $all->start_time=$row3['start_time'];
  $all->rule_time=$row3['rule_time'];
  $all->fail_status=$row3['fail_status'];
  $all->status_b='1';
  array_push($data,$all);
}

echo json_encode($data,JSON_UNESCAPED_UNICODE);
mysqli_free_result($result);
mysqli_free_result($result1);
mysqli_close($conn);


