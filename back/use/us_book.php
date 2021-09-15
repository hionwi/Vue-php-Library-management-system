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
//$ISBN='9781451031560';
//$b_name='%';
//$author='%';
//$type_name='%';
//连接数据库
//$conn=new mysqli('42.192.40.242','test','1234qwerasdf','bianquan');
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');
$data=array();

class us_Book{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $status;
  public $status_b;
}

class us_n_BOOK{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $status;
  public $rule_time;
  public $status_b;
}

if($ISBN!='%'){
  $sql5="select ISBN,b_name,author,type_name,status
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";
  $result5=$conn->query($sql5);
  $re=mysqli_num_rows($result5);
  if($re==0)
  {
    $row['status_b']='0';
    //echo (json_encode($row));
    array_push($data,$row);
    mysqli_close($conn);
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    exit;
  }
  $row5=mysqli_fetch_array($result5,MYSQLI_ASSOC);
  mysqli_free_result($result5);
  if($row5['status']==1){
    $book=new us_Book();
    $book->status_b='1';
    $book->ISBN=$row5['ISBN'];
    $book->b_name=$row5['b_name'];
    $book->author=$row5['author'];
    $book->type_name=$row5['type_name'];
    $book->status=$row5['status'];
    array_push($data,$book);
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    mysqli_close($conn);
    exit;
  }
  else{
    $sql3="select ISBN,b_name,author,type_name,status
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";
    $sql4="select rule_time
          from (user_books ub left join user u on ub.u_ID=u.u_ID)
          where (ISBN=$ISBN)";
    $result3=$conn->query($sql3);
    $result4=$conn->query($sql4);
    $row3=mysqli_fetch_array($result3,MYSQLI_ASSOC);
    $row4=mysqli_fetch_array($result4,MYSQLI_ASSOC);
    $u_books=new us_n_BOOK();
//    array_push($data,$row1);
//    array_push($data,$row2);
    $u_books->status_b='1';
    $u_books->ISBN=$row3['ISBN'];
    $u_books->b_name=$row3['b_name'];
    $u_books->author=$row3['author'];
    $u_books->type_name=$row3['type_name'];
    $u_books->status=$row3['status'];
    $u_books->rule_time=$row4['rule_time'];
    array_push($data,$u_books);
    //echo (json_encode($u_books,JSON_UNESCAPED_UNICODE));
    mysqli_free_result($result3);
    mysqli_free_result($result4);
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    mysqli_close($conn);
    exit;
  }
}

if($type_name=='%')
{
  $r='%';
}
else{
  $sql1="select type_ID from type_b where type_name='$type_name'";
  $result1=$conn->query($sql1);
  $row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
  $r=$row1['type_ID'];
}

//$row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
//mysqli_free_result($result1);
//$r=$row1['type_ID'];

$sql="select ISBN,status from books where (ISBN like '$ISBN') and (b_name like '$b_name') and (author like '$author') and (type_ID like '$r')";
$result=$conn->query($sql);
$re=mysqli_num_rows($result);
if($re==0)
{//未查询到
  $row['status_b']='0';
  //echo (json_encode($row));
  array_push($data,$row);
  mysqli_close($conn);
  echo json_encode($data,JSON_UNESCAPED_UNICODE);
  exit;
}


while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
  $ISBN=$row['ISBN'];
  $sql2="select ISBN,b_name,author,type_name,status
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN='$ISBN')";

  if($row['status']==1){
    $result2=$conn->query($sql2);
    $row2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
    $book=new us_Book();
    $book->status_b='1';
    $book->ISBN=$row2['ISBN'];
    $book->b_name=$row2['b_name'];
    $book->author=$row2['author'];
    $book->type_name=$row2['type_name'];
    $book->status=$row2['status'];
    array_push($data,$book);
    //echo (json_encode($book,JSON_UNESCAPED_UNICODE));
  }
  else{
    $sql3="select ISBN,b_name,author,type_name,status
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN='$ISBN')";
    $sql4="select rule_time
          from (user_books ub left join user u on ub.u_ID=u.u_ID)
          where (ISBN=$ISBN)";
    $result3=$conn->query($sql3);
    $result4=$conn->query($sql4);
    $row3=mysqli_fetch_array($result3,MYSQLI_ASSOC);
    $row4=mysqli_fetch_array($result4,MYSQLI_ASSOC);
    $u_books=new us_n_BOOK();
//    array_push($data,$row1);
//    array_push($data,$row2);
    $u_books->status_b='1';
    $u_books->ISBN=$row3['ISBN'];
    $u_books->b_name=$row3['b_name'];
    $u_books->author=$row3['author'];
    $u_books->type_name=$row3['type_name'];
    $u_books->status=$row3['status'];
    $u_books->rule_time=$row4['rule_time'];
    array_push($data,$u_books);
    //echo (json_encode($u_books,JSON_UNESCAPED_UNICODE));
    mysqli_free_result($result3);
    mysqli_free_result($result4);
  }
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);
mysqli_free_result($result);
mysqli_close($conn);

