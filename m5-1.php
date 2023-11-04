<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "utf-8";>
        <title>mission5-1</title>
    </head>
    <body>
        
        <?php
            //データベースに接続
            $dsn = "mysql:dbname=******;host=localhost";
            $user = "******";
            $password = "******";
            $pdo = new PDO ($dsn, $user, $password, array (PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            //テーブル作成
            $sql = "CREATE TABLE IF NOT EXISTS mission5"
            ."("
            ."number INT AUTO_INCREMENT PRIMARY KEY,"
            ."name CHAR(32),"
            ."comment TEXT,"
            ."date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,"
            ."password varchar(255)"
            .");";
            $stmt = $pdo -> query($sql);
            
            
            //テーブル削除
            //$sql = "DROP TABLE mission5";
            //$stmt = $pdo -> query($sql);
            
            //新規投稿
            if (!empty ($_POST["name"]) && !empty ($_POST["comment"])) {
                if (empty ($_POST["hidden"]) && !empty ($_POST["pass"])) {
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $pass = $_POST["pass"];
                    $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
                    
                    //テーブルにデータを入力
                    $sql = "INSERT INTO mission5 (name, comment, password) VALUES (:name, :comment, :hasedPass)";
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(":name", $name, PDO::PARAM_STR);
                    $stmt -> bindParam(":comment", $comment, PDO::PARAM_STR);
                    $stmt -> bindParam(":hasedPass", $hashedPass, PDO::PARAM_STR);
                    $stmt -> execute();
                    
                    //編集内容書き換え
                } else {
                    $hidden = $_POST["hidden"];
                    $newName = $_POST["name"];
                    $newComment = $_POST["comment"];
                    
                    $sql = "UPDATE mission5 SET name = :newName, comment = :newComment WHERE number = :hidden";
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(":newName", $newName, PDO::PARAM_STR);
                    $stmt -> bindParam(":newComment", $newComment, PDO::PARAM_STR);
                    $stmt -> bindParam(":hidden", $hidden, PDO::PARAM_INT);
                    $stmt -> execute();
                    
                }
                
            //削除機能    
            } elseif (!empty ($_POST["deleteNum"]) && !empty ($_POST["dPass"])) {
                //パスワード認証
                $deleteNum = $_POST["deleteNum"];
                $dPass = $_POST["dPass"];
                $sql = "SELECT * FROM mission5 WHERE number = :deleteNum";
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindParam(":deleteNum", $deleteNum, PDO::PARAM_INT);
                $stmt -> execute();
                $dResults = $stmt -> fetch(PDO::FETCH_ASSOC);
                
                //パスワードが合っていれば削除実行
                if (password_verify($dPass, $dResults["password"])) {
                    $sql = "DELETE FROM mission5 WHERE number = :deleteNum";
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(":deleteNum", $deleteNum, PDO::PARAM_INT);
                    $stmt -> execute();
                    
                } else {
                    echo "パスワードが間違っています";
                }
                
            //編集機能    
            } elseif (!empty ($_POST["editNum"]) && !empty ($_POST["ePass"])) {
                $editNum = $_POST["editNum"];
                $ePass = $_POST["ePass"];
                $sql = "SELECT * FROM mission5 WHERE number = :editNum";
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindParam(":editNum", $editNum, PDO::PARAM_INT);
                $stmt -> execute();
                $eResults = $stmt -> fetch(PDO::FETCH_ASSOC);
                
            }
        ?>
        
        <form action = "", method = "POST">
            <p>＜新規投稿＞</p>
            <input type = "text" name = "name" placeholder = "名前" value = "<?php if(isset($eResults) && password_verify($ePass, $eResults["password"])) {echo $eResults["name"];} ?>"><br>
            <input type = "text" name = "comment" placeholder = "コメント" value = "<?php if(isset($eResults) && password_verify($ePass, $eResults["password"])) {echo $eResults["comment"];} ?>"><br>
            <input type = "password" name = "pass" placeholder = "パスワード"><br>
            <input type = "hidden" name = "hidden" value = "<?php if(isset($eResults) && password_verify($ePass, $eResults["password"])) {echo $eResults["number"];} ?>">
            <input type = "submit" name = "submit" value = "送信"><br>
            <p>＜削除＞</p>
            <input type = "number" name = "deleteNum" placeholder = "削除番号"><br>
            <input type = "password" name = "dPass" placeholder = "パスワード"><br>
            <input type = "submit" name = "dSubmit" value = "削除"><br>
            <p>＜編集＞</p>
            <input type = "number" name = "editNum" placeholder = "編集番号"><br>
            <input type = "password" name = "ePass" placeholder = "パスワード"><br>
            <input type = "submit" name = "eSubmit" value = "編集"><br><hr>
        </form>
        
        <?php
            //データを表示  一つでもレコードがあれば表示
            if ("SELECT EXISTS (SELECT * FROM mission5 WHERE number = 1)") {
                $sql = "SELECT * FROM mission5";
                $stmt = $pdo -> query($sql);
                $results = $stmt -> fetchAll();
                    foreach ($results as $row) {
                        echo $row["number"]." ";
                        echo $row["name"]." ";
                        echo $row["comment"]." ";
                        echo $row["date"]." ";
                        echo "<br>";
                        echo "<hr>";
                }
            } else {
                //何もしない
            }

        ?>
        
    </body>
</html>