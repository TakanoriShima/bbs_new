<?php
    // 外部ファイルの読み込み
    // require_once 'config/const.php';
    // require_once 'models/user.php';
    require_once 'utils/UserDAO.php';
    require_once 'utils/MessageDAO.php';
    require_once 'utils/CommentDAO.php';
    
    // セッションスタート
    session_start();
    
    // 変数の初期化
    $user_id = "";
    $id = "";
    $message = "";
    $flash_message = "";
    
    // ログインしているのならば
    if(isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
    }
    
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
            $content = $_POST['content'];
                    
            // データベースを扱う便利なインスタンス生成
            $comment_dao = new CommentDAO();

            // 新しいコメントインスタンス作成
            $comment = new Comment($user_id, $id, $content);
            $comment_dao->insert($comment);             
                   
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
        $user_dao = new UserDAO();
        if($user_id !== ""){
            $me = $user_dao->get_user_by_id($user_id);
        }
        $message_dao = new MessageDAO();
        // テーブルから1件のデータを取得
        $message = $message_dao->get_message_by_id($id);
       
        // 便利なインスタンス削除
        $message_dao = null;
        
        $comment_dao = new CommentDAO();
        $comments = $comment_dao->get_all_comemnts_by_message_id($id);
        $comment_dao = null;
  
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
        <style>
            .avatar_image{
                object-fit: cover;
                border-radius: 50%;
                width: 100px;
                height: 100px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            
            <?php if($user_id !== ""){ ?>
            <div class="row mt-4">
                <div class="col-sm-2">
                    <img src="<?php print USER_IMAGE_DIR . $me->avatar ?>" class="avatar_image">
                </div>
                <div class="col-sm-2 mt-4">
                    <?php print $me->nickname; ?>さん
                </div>
                <div class="offset-sm-6 col-sm-2">
                    <a href="logout.php" class="btn btn-primary">ログアウト</a>
                </div>
            </div>
            <?php } ?>
            
            <div class="row mt-2">
                <h1 class="text-center col-sm-12 mt-2">id: <?php print $id; ?> の投稿詳細</h1>
            </div>
            <?php if($user_id !== "" && $message->user_id === $user_id){ ?>
            <div class="row mt-2">
                <h4 class="text-center col-sm-12"><a href="edit.php?id=<?php print $id; ?>" class="btn btn-primary">更新・削除ページへ</a></h4>
            </div>
            <?php } ?>
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h1>
            </div>
            <div class="row mt-3">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th class="text-center">投稿者</th>
                        <?php $user = $user_dao->get_user_by_id($message->user_id); ?>
                        <td class="text-center"><?php print $user->nickname; ?></td>
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
                            <img src="<?php print POST_IMAGE_DIR . $message-> image?>">
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
                        <?php $user = $user_dao->get_user_by_id($comment->user_id); ?>
                        <td><?php print $user->nickname; ?></td>
                        <td><?php print $comment->content; ?></td>
                        <td><?php print $comment->created_at; ?></td>
                    </tr>
                <?php } ?>
                </table>
            </div>
            <?php }else{ ?>
             <div class="row mt-4">
                <p class="offset-sm-2 col-sm-8">コメントはまだありません。</p>
            </div>
            <?php } ?>

            <?php if($user_id !== ""){ ?>
            <div class="row mt-2 comments">
                <form class="offset-sm-2 col-sm-8" action="show.php" method="POST">
                    <div class="row">
                        <div class="form-group col-sm-8">
                            <label class="col-sm-3 col-form-label">コメント</label>
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
            <?php } ?>
                   
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