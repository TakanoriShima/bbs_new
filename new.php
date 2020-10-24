<?php
    // 外部ファイルの読み込み
    require_once 'utils/MessageDAO.php';
    
    // セッション開始
    session_start();
    
    $user_id = $_SESSION["user_id"];
    
    // 変数の初期化
    $flash_message = "";
    
    // POST通信ならば（= 新規投稿ボタンが押された時）
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        // フォームからの入力値を取得
        $title = $_POST['title'];
        $body = $_POST['body'];

        
        // 例外処理
        try {
            
            // データベースを扱う便利なインスタンス生成
            $message_dao = new MessageDAO();
            // 画像ファイルの物理的アップロード処理
            $image = $message_dao->upload($_FILES);
            
            // 新しいメッセージインスタンスを生成
            $message = new Message($user_id, $title, $body, $image);

            // データベースにデータを1件保存
            $message_dao->insert($message);
            
            // 便利なインスタンス削除
            $message_dao = null;
                    
            // セッションにフラッシュメッセージを保存        
            $_SESSION['flash_message'] = "投稿が成功しました。";
            
            // 画面遷移
            header('Location: index.php');

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
        <title>新規投稿</title>
    </head>
    <body>
        <div class="container">
            <div class="row mt-2">
                <h1 class="text-center col-sm-12 mt-2">新規投稿</h1>
            </div>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h1>
            </div>
            <div class="row mt-2">
                <form class="col-sm-12" action="new.php" method="POST" enctype="multipart/form-data">

                    <!-- 1行 -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">タイトル</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="title" required>
                        </div>
                    </div>
                    
                    <!-- 1行 -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">内容</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="body" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">画像アップロード</label>
                        <div class="col-sm-2">
                            <input type="file" name="image" required accept='image/*' onchange="previewImage(this);">
                        </div>
                        <canvas id="canvas" class="offset-sm-4 col-4" width="0" height="0"></canvas>
                    </div>
                    
                    <!-- 1行 -->
                    <div class="form-group row">
                       <button type="submit" class="offset-sm-2 col-sm-10 btn btn-danger " id="upload">投稿</button>
                    </div>
                </form>
            </div>
             <div class="row mt-5">
                <a href="index.php" class="btn btn-primary">投稿一覧へ</a>
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