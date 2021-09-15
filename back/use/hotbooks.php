<?php

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");
//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');

//$conn=new mysqli('42.192.40.242','test','1234QWer!@#$','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

$data=array();

class hot_book1{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $start_time;
  public $rule_time;
  public $status;
  public $status_b;
}

class hot_book2{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $status;
  public $status_b;
}

$sql="select ISBN,status from books order by b_number desc limit 0,10";
$result=$conn->query($sql);
while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
  $ISBN=$row['ISBN'];
  $sql2="select ISBN,b_name,author,type_name,status
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN='$ISBN')";
  if($row['status']==1)
  {
    $result2=$conn->query($sql2);
    $row2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
    $book=new hot_book2();
    $book->status_b='1';
    $book->ISBN=$row2['ISBN'];
    $book->b_name=$row2['b_name'];
    $book->author=$row2['author'];
    $book->type_name=$row2['type_name'];
    $book->status=$row2['status'];
    array_push($data,$book);
  }
  else{
    $sql3="select ISBN,b_name,author,type_name,status
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN='$ISBN')";
    $sql4="select start_time,rule_time
          from (user_books ub left join user u on ub.u_ID=u.u_ID)
          where (ISBN=$ISBN)";
    $result3=$conn->query($sql3);
    $result4=$conn->query($sql4);
    $row3=mysqli_fetch_array($result3,MYSQLI_ASSOC);
    $row4=mysqli_fetch_array($result4,MYSQLI_ASSOC);
    $u_books=new hot_book1();
//    array_push($data,$row1);
//    array_push($data,$row2);
    $u_books->status_b='1';
    $u_books->ISBN=$row3['ISBN'];
    $u_books->b_name=$row3['b_name'];
    $u_books->author=$row3['author'];
    $u_books->type_name=$row3['type_name'];
    $u_books->status=$row3['status'];
    $u_books->rule_time=$row4['rule_time'];
    $u_books->start_time=$row4['start_time'];
    array_push($data,$u_books);
    //echo (json_encode($u_books,JSON_UNESCAPED_UNICODE));
    mysqli_free_result($result3);
    mysqli_free_result($result4);
  }
}

mysqli_free_result($result);
$conn->close();
echo json_encode($data,JSON_UNESCAPED_UNICODE);
