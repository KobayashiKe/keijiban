<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>5-1-1</title>
</head>
<body>
    <h3>掲示板</h3>
    <?php
        // DB接続設定
        $dsn = 'データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));        
    
        //テーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS tb511ver3"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "pass char(32),"
        . "date TEXT"
        .");";
        $stmt = $pdo->query($sql);

        $edidata0 = "";
        $edidata1 = "";
        $edidata2 = "";
       
    //投稿
        //名前とかがが入力されてないとき
        if(empty($_POST["delete"]) && empty($_POST["edit"]) && empty($_POST["editnow"])) {
            if(empty($_POST["name"]) && empty($_POST["comment"]) && empty($_POST["pass1"])) {
                echo "<br>";
            } elseif(empty($_POST["name"])) {
                echo "名前を入力してください" . "<br>";
            } elseif(empty($_POST["comment"])) {
                echo "コメントを入力してください" . "<br>";
            } elseif(empty($_POST["pass1"])) {
                echo "パスワードを入力してください" . "<br>";
            //名前とコメントが入力されてて編集番号が入力されてないとき
            } else {        
                //名前とコメントとパスワードのデータを入力
                $sql = $pdo -> prepare("INSERT INTO tb511ver3 (name,comment,pass,date) VALUES (:name,:comment,:pass,:date)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $name = $_POST["name"];
                $comment = $_POST["comment"]; 
                $pass = $_POST["pass1"];
                $date = date("Y/m/d H:i:s");
                $sql -> execute();        
            }
        }
    
    //削除
        if(!empty($_POST["delete"]) && !empty($_POST["pass2"])) {  //削除のとこに入力されてるとき
            $sql = 'SELECT * FROM tb511ver3';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if($row['id']==$_POST["delete"] && $row['pass']==$_POST['pass2']) {
                    $id = $_POST["delete"];
                    $sql = 'delete from tb511ver3 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();  
                }
            }
        }
        
    //編集
        //選ぶとき
        //編集のとこに入力されてるときファイル読み込み
        if(!empty($_POST["edit"])) {  
            $sql = 'SELECT * FROM tb511ver3';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if($row['id']==$_POST["edit"] && $row['pass']==$_POST["pass3"]) {
                    $edidata0 = $row['id'];
                    $edidata1 = $row['name'];
                    $edidata2 = $row['comment'];
                } elseif($row['id']==$_POST["edit"] && $row['pass']!=$_POST["pass3"]) {
                    echo "パスワードが違います";
                }
            }            
        }    

        //上書きするとき
        if(!empty($_POST["editnow"]) && !empty($_POST["name"]) && !empty($_POST["comment"])) {
            $sql = 'SELECT * FROM tb511ver3';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                //編集中
                if($row['id']==$_POST["editnow"] && !empty($_POST["pass1"])) {
                    $id = $_POST["editnow"]; 
                    $name = $_POST["name"];
                    $comment = $_POST["comment"]; 
                    $pass = $_POST["pass3"];
                    $sql = 'UPDATE tb511ver3 SET name=:name,comment=:comment WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();        
                }elseif($row['id']==$_POST["editnow"] && empty($_POST["pass1"])) {
                    echo "パスワードを入力してください";
                }
            }
        }            
    ?>


    <form action="" method="post">
        【投稿フォーム】<br>
        <input type="text" name="name" placeholder="名前" value=<?php echo $edidata1;?>><br>
        <input type="text" name="comment" placeholder="コメント" value="<?php echo $edidata2;?>"><br>
        <input type="text" name="pass1" placeholder="パスワード">
        <input type="submit" value="送信"><br>
        <input type="hidden" name="editnow" placeholder="編集中の番号" value="<?php echo $edidata0;?>"><br>
        【削除フォーム】<br>
        <input type="text" name="delete" placeholder="削除する番号"><br>
        <input type="text" name="pass2" placeholder="パスワード">
        <input type="submit" value="削除"><br>
        【編集フォーム】<br>
        <input type="text" name="edit" placeholder="編集する番号"><br>
        <input type="text" name="pass3" placeholder="パスワード">
        <input type="submit" value="編集"><br>
    </form>

    <?php
        //表示 
        $sql = 'SELECT * FROM tb511ver3';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].'<br>';
        echo "<hr>";
        }
    ?>
</body>
</html>