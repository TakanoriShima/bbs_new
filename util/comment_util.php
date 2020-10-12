<?php
// 外部ファイルの読み込み
require_once 'config/const.php';
require_once 'models/comment.php';

// データベースとやり取りを行う便利なクラス
class comment_util{
    
    // データベースと接続を行うメソッド
    public function get_connection(){
        $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
        return $pdo;
    }
    
    // データベースとの切断を行うメソッド
    public function close_connection($pdo, $stmp){
        $pdo = null;
        $stmp = null;
    }
    
    // message_idを指定して、全テーブル情報を取得するメソッド
    public function get_all_comemnts_by_message_id($message_id){
        $pdo = $this->get_connection();
        //$stmt = $pdo->prepare('SELECT messages.id AS message_id, comments.id AS comment_id, comments.name AS name, comments.content AS comment, comments.created_at AS created_at FROM comments JOIN messages ON comments.message_id = messages.id WHERE comments.message_id=:message_id ORDER BY comments.id DESC');
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE message_id=:message_id");
        print $message_id;
        $stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
        $stmt->execute();
        // フェッチの結果を、messageクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'comment');
        $comments = $stmt->fetchAll();
        
        $this->close_connection($pdo, $stmp);
        // コメントクラスのインスタンスの配列を返す
        return $comments;
    }
    
    // データを1件登録するメソッド
    public function insert($comment){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("INSERT INTO comments (message_id, name, content) VALUES (:message_id, :name, :content)");

        // バインド処理
        $stmt->bindParam(':message_id', $comment->message_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $comment->name, PDO::PARAM_STR);
        $stmt->bindParam(':content', $comment->content, PDO::PARAM_STR);

        $stmt->execute();
        
        $this->close_connection($pdo, $stmp);
    }
}
