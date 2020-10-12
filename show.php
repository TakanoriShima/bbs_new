<?php
    // 外部ファイルの読み込み
    require_once 'util/message_util.php';
    require_once 'util/comment_util.php';
    
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


    // POST通信の時 (= 更新、もしくは削除ボタンが押された時)  
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        // 例外処理
        try {
            // 入力された値を取得
            $name = $_POST['name'];
            $content = $_POST['content'];
                    
            // データベースを扱う便利なインスタンス生成
            $comment_util = new comment_util();

            // 新しいコメントインスタンス作成
            $comment = new comment($id, $name, $content);
            $comment_util->insert($comment);             
                   
            // セッションにフラッシュメッセージをセット 
            $_SESSION['flash_message'] = "コメントが投稿されました。";
            
            // 画面遷移
            header('Location: show.php?id=' . $id);

        } catch (PDOException $e) {
            echo 'PDO exception: ' . $e->getMessage();
            exit;
        }
   
    }

    // 例外処理
    try {
        // データベースを扱う便利なインスタンス生成
        $message_util = new message_util();
        // テーブルから1件のデータを取得
        $message = $message_util->get_message_by_id($id);
       
        // 便利なインスタンス削除
        $message_util = null;
        
        $comment_util = new comment_util();
        $comments = $comment_util->get_all_comemnts_by_message_id($id);
        $comment_util = null;
  
    } catch (PDOException $e) {
        echo 'PDO exception: ' . $e->getMessage();
        exit;
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
        <title>投稿詳細</title>
    </head>
    <body>
        <div class="container">
            <div class="row mt-2">
                <h1 class="text-center col-sm-12 mt-2">id: <?php print $id; ?> の投稿詳細</h1>
            </div>
            <div class="row mt-2">
                <h4 class="text-center col-sm-12"><a href="edit.php?id=<?php print $id; ?>" class="btn btn-primary">更新・削除ページへ</a></h4>
            </div>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h1>
            </div>
            <div class="row mt-3">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th class="text-center">投稿者</th>
                        <td class="text-center"><?php print $message->name; ?></td>
                    </tr>
                    <tr>
                        <th class="text-center">投稿日時</th>
                        <td class="text-center"><?php print $message->created_at; ?></td>
                    </tr>
                    <tr>
                        <th class="text-center">タイトル</th>
                        <td class="text-center"><?php print $message->title; ?></td>
                    </tr>
                    <tr>
                        <th class="text-center">内容</th>
                        <td class="text-center"><?php print $message->body; ?></td>
                    </tr>
                    <tr>
                        <th class="text-center">画像</th>
                        <td class="text-center">
                            <img src="<?php print IMAGE_DIR . $message-> image?>">
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="row mt-2">
                <h3 class="text-center offset-sm-2 col-sm-8 mt-2">コメント一覧</h3>
            </div>
            <!--データが1件でもあれば-->
            <?php if(count($comments) !== 0){ ?> 
            <div class="row mt-4">
                <p class="offset-sm-2 col-sm-8"><?php print count($comments); ?>件</p>
            </div>
            <div class="row mt-2">
                <table class="offset-sm-2 col-sm-8 table table-bordered table-striped">
                    <tr>
                        <th>ID</th>
                        <th>ユーザ名</th>
                        <th>コメント</th>
                        <th>投稿時間</th>
                    </tr>
                    </tr>
                <?php foreach($comments as $comment){ ?>
                    <tr>
                        <td><?php print $comment->id; ?></a></td>
                        <td><?php print $comment->name; ?></td>
                        <td><?php print $comment->content; ?></td>
                        <td><?php print $message->created_at; ?></td>
                    </tr>
                <?php } ?>
                </table>
            </div>
            <?php }else{ ?>
             <div class="row mt-4">
                <p class="offset-sm-2 col-sm-8">コメントはまだありません。</p>
            </div>
            <?php } ?>

            <div class="row mt-2 comments">
                <form class="offset-sm-2 col-sm-8" action="show.php" method="POST">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="col-sm-4 col-form-label">名前</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="name" required value="<?php print ""; ?>">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label class="col-sm-6 col-form-label">コメント</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="content" required value="<?php print ""; ?>">
                            </div>
                        </div>
                        <div class="form-group col-sm-3 mt-3">
                            <label class="col-sm-6 col-form-label"></label>
                            <div class="col-sm-12">
                                <button type="submit" class="form-control btn btn-primary">投稿</button>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="<?php print $message->id; ?>">
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