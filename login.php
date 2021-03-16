
<?php
session_start();
//入力しているかどうかのチェック
if(
    !isset($_POST["name"]) || $_POST["name"]=="" ||
    !isset($_POST["email"]) || $_POST["email"]=="" ||
    !isset($_POST["password"]) || $_POST["password"]==""
){
    header("Location: main-login.php");
    exit;
}

//POSTデータの取得
$name  = $_POST["name"];
$email = $_POST["email"];
$password = $_POST["password"];

//OB接続します(mysqlを他のデータベースに変えることも可能)
try {
    $pdo = new PDO('mysql:dbname=gs_db;host=localhost;charset=utf8','root','root', array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
    $stmt = $pdo->prepare('SELECT email FROM gs_an_table WHERE email = ?');
    $stmt->execute(array($email));

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);

            // 入力したIDのユーザー名を取得
            $id = $row['id'];
            //入力したIDからユーザー名を取得
            $sql = 'SELECT * FROM gs_an_table WHERE id = $id';
            $stmt = $pdo->query($sql);
            foreach ($stmt as $row) {
                $row['name'];  // ユーザー名
            }
            $_SESSION["NAME"] = $row['name'];
            // メイン画面へ遷移
            header("Location: top-page.php");
            exit();
        } else {
            $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
        }
    } else {
        // 認証成功なら、セッションIDを新規に発行する
        // 該当データなし
        $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
    }
} catch (PDOException $e) {
    echo 'DB接続エラー！: ' . $e->getMessage();
    //$errorMessage = $sql;
    // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
    // echo $e->getMessage();
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






