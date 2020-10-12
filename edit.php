<?php
    // 外部ファイルの読み込み
    require_once 'util/message_util.php';
    
    // セッションスタート
    session_start();
    
    // 変数の初期化
    $id = "";
    $message = "";
    $flash_message = "";
    
    // id値を取得
    if(isset($_GET['id']) === true){
        $id = $_GET['id'];
    }else if(isset($_POST['id']) === true){
        $id = $_POST['id'];
    }        
    
    // フラッシュメッセージをセッションから取得し、セッション情報を削除
    if(isset($_SESSION['flash_message']) === true){
        $flash_message = $_SESSION['flash_message'];
        $_SESSION['flash_message'] = null;
    }

    // 例外処理
    try {
        // データベースを扱う便利なインスタンス生成
        $message_util = new message_util();
        // テーブルから1件のデータを取得
        $message = $message_util->get_message_by_id($id);
        
        // 便利なインスタンス削除
        $message_util = null;
        
    } catch (PDOException $e) {
        echo 'PDO exception: ' . $e->getMessage();
        exit;
    }
    
    // POST通信の時 (= 更新、もしくは削除ボタンが押された時)  
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        // 入力されたパスワード取得
        $password = $_POST['password'];
        
        try {
            // パスワードが正しいのならば
            if($password === $message->password){
                // 更新ボタンが押されたならば
                if($_POST['kind_method'] === 'update'){
                    
                    // 入力された値を取得
                    $name = $_POST['name'];
                    $title = $_POST['title'];
                    $body = $_POST['body'];
                    
                    // 画像をアップロード
                    // データベースを扱う便利なインスタンス生成
                    $message_util = new message_util();
                    $image = $message_util->upload($_FILES);
            
                    // 画像が選択されていないならば、画像ファイル名には現在の名前をセット
                    if($image === null){
                        $image = $message->image;
                    }
                
                    // 新しいメッセージインスタンス作成
                    $update_message = new message($name, $title, $body, $image, $password);
                    
                    // 更新処理
                    $message_util->update($id, $update_message);
                    
                    // 便利なインスタンス削除
                    $message_util = null;
                   
                    // セッションにフラッシュメッセージをセット 
                    $_SESSION['flash_message'] = "投稿が更新されました。";
                    
                    // トップ画面に遷移
                    header('Location: index.php');

                 // 削除ボタンが押されたならば
                }else if($_POST['kind_method'] === 'delete'){
                    // データベースを扱う便利なインスタンス生成
                    $message_util = new message_util();
                    
                    // 削除処理
                    $message_util->delete($id);
                    
                    // 便利なインスタンス削除
                    $message_util = null;
                    
                    $_SESSION['flash_message'] = "投稿が削除されました。";
                    header('Location: index.php');
                }    
            }else{
                $_SESSION['flash_message'] = "パスワードが間違えています。";
                header('Location: edit.php?id=' . $id);
            }
            
        } catch (PDOException $e) {
            echo 'PDO exception: ' . $e->getMessage();
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="favicon.ico">
        <title>投稿の更新・削除</title>
    </head>
    <body>
        <div class="container">
            <div class="row mt-2">
                <h1 class="text-center col-sm-12 mt-2">id: <?php print $id; ?> の投稿の更新・削除</h1>
            </div>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h1>
            </div>
            <div class="row mt-2">
                <form class="col-sm-12" action="edit.php?id=<?php print $id; ?>" method="POST" enctype="multipart/form-data">
               
                    <!-- 1行 -->
                    <div class="form-group row">
                        <label class="col-2 col-form-label">名前</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="name" required value="<?php print $message->name; ?>">
                        </div>
                    </div>
                
                    <!-- 1行 -->
                    <div class="form-group row">
                        <label class="col-2 col-form-label">タイトル</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="title" required value="<?php print $message->title; ?>";>
                        </div>
                    </div>
                    
                    <!-- 1行 -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">内容</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="body" required value="<?php print $message->body; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-2 col-form-label">現在の画像</label>
                        <div class="col-10">
                            
                            <img src="<?php if(file_exists(IMAGE_DIR . $message->image)){ print IMAGE_DIR . $message->image; }else{ print 'no-image.png';} ?>" alt="表示する画像がありません。">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-2 col-form-label">画像アップロード</label>
                        <div class="col-2">
                            <input type="file" name="image" accept='image/*' onchange="previewImage(this);">
                        </div>
                        <canvas id="canvas" class="offset-sm-4 col-2" width="0" height="0"></canvas>
                    </div>
                    
                    
                    <div class="row">
                        <input type="hidden" name="id" value="<?php print $message->id; ?>">
                    </div>
                    
                    <div class="row">
                        <label class="form-group col-2 col-form-label">更新・削除パスワード</label>
                        <div class="col-10">
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                
                    <!-- 1行 -->
                    <div class="form-group row mt-3">
                        <div class="offset-sm-3 col-sm-4">
                            <button type="submit" name="kind_method" value="update" class="form-control btn btn-primary">更新</button>
                        </div>
                        <div class="col-sm-4">
                            <button type="submit" name="kind_method" value="delete" class="form-control btn btn-danger" onclick="return confirm('投稿を削除します。よろしいですか？')">削除</button>
                        </div>
                    </div>
                </form>
             <div class="row mt-5">
                <a href="show.php?id=<?php print $id; ?>" class="btn btn-primary">投稿詳細へ</a>
            </div>
        </div>
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS, then Font Awesome -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js"></script>
        <script src="script.js"></script>
    </body>
</html>