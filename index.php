<?php
    // 外部ファイルの読み込み
    require_once 'config/const.php';
    require_once 'models/user.php';
    require_once 'util/user_util.php';
    require_once 'util/message_util.php';
    
    // セッションスタート
    session_start();
    
    // 変数の初期化
    $user_id = "";
    $messages = array();
    $flash_message = "";
    
    // ログインしているのならば
    if(isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
    }
    
    // 例外処理
    try {
        
        // データベースを扱う便利なインスタンス生成
        $user_util = new user_util();
        if($user_id !== ""){
            $me = $user_util->get_user_by_id($user_id);
        }
        
        $message_util = new message_util();
        // データを全件取得
        $messages = $message_util->get_all_messages();
        
        // 便利なインスタンス削除
        $message_util = null;
        
        // フラッシュメッセージの取得とセッションからの削除
        if(isset($_SESSION['flash_message']) === true){
            $flash_message = $_SESSION['flash_message'];
            $_SESSION['flash_message'] = null;
        }
        
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
        <title>簡易掲示板</title>
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
            <?php }else{ ?>
                <div class="row mt-4">
                    <div class="offset-sm-8 col-sm-2">
                        <a href="signup.php" class="btn btn-primary">会員登録</a>
                    </div>
                    <div class="col-sm-2">
                        <a href="login.php" class="btn btn-primary">ログイン</a>
                    </div>
                </div>
            <?php } ?>
            
            <div class="row mt-4">
                <h1 class="col-sm-12 text-center mt-2">投稿一覧</h1>
            </div>
            
            <div class="row mt-2">
                <h2 class="text-center col-sm-12"><?php print $flash_message; ?></h2>
            </div>
            <div class="row mt-2">
            <!--データが1件でもあれば-->
            <?php if(count($messages) !== 0){ ?> 
            
                <p><?php print count($messages); ?>件</p>
                <table class="col-sm-12 table table-bordered table-striped">
                    <tr>
                        <th>ID</th>
                        <th>ユーザ名</th>
                        <th>タイトル</th>
                        <th>内容</th>
                        <th>投稿時間</th>
                    </tr>
                    </tr>
                <?php foreach($messages as $message){ ?>
                    <tr>
                        <td><a href="show.php?id=<?php print $message->id; ?>"><?php print $message->id; ?></a></td>
                        <?php $user = $user_util->get_user_by_id($message->user_id); ?>
                        <td><?php print $user->nickname; ?></td>
                        <td><?php print $message->title; ?></td>
                        <td><?php print $message->body; ?></td>
                        <td><?php print $message->created_at; ?></td>
                    </tr>
                <?php } ?>
                </table>
            <?php }else{ ?>
                    <p>データは、1件もありません。</p>
            <?php } ?>
            </div>
            <?php if($user_id !== ""){ ?>
            <div class="row mt-5">
                <a href="new.php" class="btn btn-primary">新規投稿</a>
            </div> 
            <?php } ?>
        </div>
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS, then Font Awesome -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js"></script>
    </body>
</html>
