<?php

//********************//
//文件名：s_nowbro.php //
//功  能：查询当前借阅信息（书名，ISBN，u_ID） //
//功能发出对象：管理员     //
//作  者：冯智洋        //
//时  间：2020/11/22  //
//版  本：1.0.0        //


header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
$ro = file_get_contents('php://input');  //得到前端的json文件
$user = json_decode($ro, true, JSON_UNESCAPED_UNICODE);    //转变成php数组

$u_ID = $user['u_ID'];//获取对应信息
$ISBN=$user['ISBN'];
$b_name=$user['b_name'];
//$u_ID='%';
//$ISBN='%';
//$b_name='%';
$conn = new mysqli('localhost', 'root', '1234qwer!@#$', 'bianquan');

//$conn=new mysqli('42.192.40.242','test','1234QWer!@#$','bianquan');
//判断连接是否成功
if ($conn->connect_error) {
  die("连接失败" . $conn->connect_error);
}
//设置编码格式避免中文乱码
mysqli_set_charset($conn, 'utf8');
$conn->query('SET NAMES UTF8');

$data = array();
class u_b  //定义查询结果信息类
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

if($ISBN!='%'){//ISBN查询
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
  $sql="select * from user_books where ISBN=$ISBN";
  $result=$conn->query($sql);
  $r=mysqli_num_rows($result);
  if($r==0)
  {//未查询到对应信息
    $row['status']=0;
    array_push($data,$row);
    mysqli_close($conn);
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    exit;
  }
  //查询结果放入关联数组中
  while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
  {
    $u_ID=$row['u_ID'];
    $sql1="select u_ID,u_name from user where u_ID='$u_ID'";
    $result1=$conn->query($sql1);
    $row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
    mysqli_free_result($result1);
    $u=new u_b();
    $u->ISBN=$row5['ISBN'];
    $u->b_name=$row5['b_name'];
    $u->author=$row5['author'];
    $u->type_name=$row5['type_name'];
    $u->u_ID=$row1['u_ID'];
    $u->u_name=$row1['u_name'];
    $u->start_time=$row['start_time'];
    $u->rule_time=$row['rule_time'];

    $u->fail_status=$row['fail_status'];

    $u->status='1';
    array_push($data,$u);
  }
  mysqli_close($conn);
  echo json_encode($data,JSON_UNESCAPED_UNICODE);  //JSON数组返回前端
  exit;
}

if($u_ID!='%')
{//u_ID查询
  $sql="select * from user_books where u_ID='$u_ID'";
  $result2=$conn->query($sql);
  $re=mysqli_num_rows($result2);
  if($re==0)
  {
    $row['status']='0';
    //echo (json_encode($row));
    array_push($data,$row);
    mysqli_close($conn);
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    exit;
  }
  $sql1="select u_ID,u_name from user where u_ID='$u_ID'";
  $result1=$conn->query($sql1);
  $row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
  mysqli_free_result($result1);
  //将结果放入关联数组中进行循环
  while($row2=mysqli_fetch_array($result2,MYSQLI_ASSOC))
  {
    $ISBN=$row2['ISBN'];
    $sql5="select ISBN,b_name,author,type_name
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";
    $result5=$conn->query($sql5);
    $row3=mysqli_fetch_array($result5);
    $u=new u_b();
    $u->ISBN=$row3['ISBN'];
    $u->b_name=$row3['b_name'];
    $u->author=$row3['author'];
    $u->type_name=$row3['type_name'];
    $u->u_ID=$row1['u_ID'];
    $u->u_name=$row1['u_name'];
    $u->start_time=$row2['start_time'];
    $u->rule_time=$row2['rule_time'];

    $u->fail_status=$row2['fail_status'];

    $u->status='1';
    array_push($data,$u);
  }
  mysqli_close($conn);
  echo json_encode($data,JSON_UNESCAPED_UNICODE);  //JSON数组返回前端
  exit;
}

if($b_name!='%')
{//书名查询
  $sql="select ISBN from books where b_name='$b_name'";
  $result3=$conn->query($sql);
  $re=mysqli_num_rows($result3);
  if($re==0)
  {
    $row['status']='0';
    //echo (json_encode($row));
    array_push($data,$row);
    mysqli_close($conn);
    echo json_encode($data,JSON_UNESCAPED_UNICODE); //JSON数组返回前端
    exit;
  }
  //将结果放入关联数组中进行循环
  while($row4=mysqli_fetch_array($result3,MYSQLI_ASSOC))
  {
    $ISBN=$row4['ISBN'];
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
    $sql="select * from user_books where ISBN=$ISBN";
    $result=$conn->query($sql);
    $r=mysqli_num_rows($result);
    if($r==0)
    {
      $row['status']=0;
      array_push($data,$row);
      mysqli_close($conn);
      echo json_encode($data,JSON_UNESCAPED_UNICODE); //JSON数组返回前端
      exit;
    }
    //将结果放入关联数组中进行循环
    while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
    {
      $u_ID=$row['u_ID'];
      $sql1="select u_ID,u_name from user where u_ID='$u_ID'";
      $result1=$conn->query($sql1);
      $row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
      mysqli_free_result($result1);
      $u=new u_b();
      $u->ISBN=$row5['ISBN'];
      $u->b_name=$row5['b_name'];
      $u->author=$row5['author'];
      $u->type_name=$row5['type_name'];
      $u->u_ID=$row1['u_ID'];
      $u->u_name=$row1['u_name'];
      $u->start_time=$row['start_time'];
      $u->rule_time=$row['rule_time'];
      $u->fail_status=$row['fail_status'];
      $u->status='1';
      array_push($data,$u);
    }
  }
  mysqli_close($conn);
  echo json_encode($data,JSON_UNESCAPED_UNICODE); //JSON数组返回前端
  exit;
}
