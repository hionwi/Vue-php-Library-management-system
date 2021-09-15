<?php
//********************//
//文件名：all_books.php //
//功  能：查询所有书籍信息 //
//功能发出对象：管理员     //
//作  者：冯智洋        //
//时  间：2020/11/22  //
//版  本：1.0.0        //


//设置请求头完成跨域访问
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Content-type:text/html;charset=utf-8");


$ro = file_get_contents('php://input');  //得到前端的json文件
$book = json_decode($ro, true,JSON_UNESCAPED_UNICODE);    //转变成php数组


//连接数据库
$conn=new mysqli('localhost','root','1234qwer!@#$','bianquan');
//判断连接是否成功
if($conn->connect_error){
  die("连接失败".$conn->connect_error);
}

//连接成功
//设置编码格式防止乱码
mysqli_set_charset($conn,'utf8');
$conn->query('SET NAMES UTF8');

//声明数组，最后结果以json数组返回前端
$data=array();

//执行sql查询语句，得到书籍ISBN，和在馆状态码
$sql="select status,ISBN from books";
$result=$conn->query($sql);
$re=mysqli_num_rows($result);

//上一条sql语句返回结果为空
if($re==0)
{//未查询到
  $row['status_b']='0';
  //echo (json_encode($row));
  array_push($data,$row);
  mysqli_close($conn);
  echo json_encode($data,JSON_UNESCAPED_UNICODE);  //将数组转码成为json格式返回前端
  exit;
}


//定义用于承接在馆书籍信息的类
class Book{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $status;
  public $status_b;
}
//定义用于承接未在馆书籍信息的类
class U_Book{
  public $ISBN;
  public $b_name;
  public $author;
  public $type_name;
  public $status;
  public $u_ID;
  public $u_name;
  public $u_number;
  public $u_fail;
  public $start_time;
  public $rule_time;
  public $status_b;
}


//将查询返回的多条结果按序放入关联数组中进行循环
while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
  $ISBN=$row['ISBN'];   //取出关联数组中的ISBN，作为进一步查询的依据


  //执行SQL语句，从对应表中获取目标信息
  $sql1="select ISBN,b_name,author,type_name,status
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";

  if($row['status']==1){//在馆
    $result1=$conn->query($sql1);
    $row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
    $book=new Book();
    $book->status_b='1';
    $book->ISBN=$row1['ISBN'];
    $book->b_name=$row1['b_name'];
    $book->author=$row1['author'];
    $book->type_name=$row1['type_name'];
    $book->status=$row1['status'];
    array_push($data,$book);  //将结果放入数组中
    //echo (json_encode($book,JSON_UNESCAPED_UNICODE));
  }
  else{//未在馆
    $sql2="select ISBN,b_name,author,type_name,status
            from (books b left join type_b t on b.type_ID=t.type_ID)
            where (ISBN=$ISBN)";
    $sql3="select u.u_ID,u_name,u_fail,u_number,start_time,rule_time
          from (user_books ub left join user u on ub.u_ID=u.u_ID)
          where (ISBN=$ISBN)";
    $result1=$conn->query($sql2);
    $result2=$conn->query($sql3);
    $row1=mysqli_fetch_array($result1,MYSQLI_ASSOC);
    $row2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
    $u_books=new U_Book();
//    array_push($data,$row1);
//    array_push($data,$row2);
    $u_books->status_b='1';
    $u_books->ISBN=$row1['ISBN'];
    $u_books->b_name=$row1['b_name'];
    $u_books->author=$row1['author'];
    $u_books->type_name=$row1['type_name'];
    $u_books->status=$row1['status'];
    $u_books->u_ID=$row2['u_ID'];
    $u_books->u_name=$row2['u_name'];
    $u_books->u_number=$row2['u_number'];
    $u_books->u_fail=$row2['u_fail'];
    $u_books->start_time=$row2['start_time'];
    $u_books->rule_time=$row2['rule_time'];
    array_push($data,$u_books);  //结果放入数组中
    //echo (json_encode($u_books,JSON_UNESCAPED_UNICODE));
    mysqli_free_result($result1);
    mysqli_free_result($result2);
  }

}
echo json_encode($data,JSON_UNESCAPED_UNICODE);//将结果数组转码成为json格式进行返回
mysqli_free_result($result);
mysqli_close($conn);  //关闭连接
