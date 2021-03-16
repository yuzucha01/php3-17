
<?php
//入力しているかどうかのチェック
if(!isset($_POST["name"]) || $_POST["name"]=="" ||
    !isset($_POST["email"]) || $_POST["email"]=="" ||
    !isset($_POST["password"]) || $_POST["password"]==""
){
    header("Location: index.php");
    exit;
}

//POSTデータの取得
$name  = $_POST["name"];
$email = $_POST["email"];
$password = $_POST["password"];

//OB接続します(mysqlを他のデータベースに変えることも可能)
try {
    $pdo = new PDO('mysql:dbname=gs_db;host=localhost;charset=utf8','root','root');
    echo "接続OK！";
  } catch (PDOException $e) {
    echo 'DB接続エラー！: ' . $e->getMessage();
  }

//データベース内のメールアドレスを取得
$stmt = $pdo->prepare("SELECT email from gs_an_table where email = ?");
$stmt->execute([$email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

//データベース内のメールアドレスと重複していない場合、登録する。
if (!isset($row['email'])) {
  //データ登録SQLの作成(POSTデータ取得で取得したデータをSQLに入れる)
    $stmt = $pdo -> prepare("INSERT INTO gs_an_table(id, name, email, password, indate )
    VALUES(NULL, :a1, :a2, :a3, sysdate())");

    $stmt->bindParam(':a1', $name, PDO::PARAM_STR); //数値の場合は PARAM_INT
    $stmt->bindParam(':a2', $email, PDO::PARAM_STR);
    $stmt->bindParam(':a3', $password, PDO::PARAM_STR);
    $status = $stmt->execute();
    header("Location: top-page.php");
    exit;
}else{
  //既に登録されたメールアドレスの場合
    header("Location: already.php");
    exit;
}

if($status==false){
//SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("QueryError:".$error[2]);

  }else{
//５．index.phpへリダイレクト
    header("Location: index.php");
    exit;
  }

  ?>






