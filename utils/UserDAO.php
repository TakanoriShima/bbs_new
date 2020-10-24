<?php
// 外部ファイルの読み込み
require_once 'config/Const.php';
require_once 'models/User.php';

// データベースとやり取りを行う便利なクラス
class UserDAO{
    
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
    
    // 全テーブル情報を取得するメソッド
    public function get_all_users(){
        $pdo = $this->get_connection();
        $stmt = $pdo->query('SELECT * FROM users');
        // フェッチの結果を、userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'user');
        $users = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // userクラスのインスタンスの配列を返す
        return $users;
    }
    
    // id値からユーザ情報を取得するメソッド
    public function get_user_by_id($id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id=:id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        // フェッチの結果を、userクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'user');
        $user = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // userクラスのインスタンスを返す
        return $user;
    }
    
    
    // 会員登録をするメソッド
    public function signup($user){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("INSERT INTO users (name, nickname, email, avatar, password) VALUES (:name, :nickname, :email, :avatar, :password)");
        // バインド処理
        $stmt->bindParam(':name', $user->name, PDO::PARAM_STR);
        $stmt->bindParam(':nickname', $user->nickname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $user->email, PDO::PARAM_STR);
        $stmt->bindParam(':avatar', $user->avatar, PDO::PARAM_STR);
        $stmt->bindParam(':password', $user->password, PDO::PARAM_STR);
        $stmt->execute();
        $this->close_connection($pdo, $stmp);
    }
    
    // ログイン処理をするメソッド
    public function login($email, $password){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email=:email AND password=:password');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'user');
        $user = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // userクラスのインスタンスを返す
        return $user;
    }
    
    // ファイルをアップロードするメソッド
    public function upload(){
        // ファイルを選択していれば
        if (!empty($_FILES['image']['name'])) {
            // ファイル名をユニーク化
            $image = uniqid(mt_rand(), true); 
            // アップロードされたファイルの拡張子を取得
            $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
            $file = USER_IMAGE_DIR . $image;
        
            // uploadディレクトリにファイル保存
            move_uploaded_file($_FILES['image']['tmp_name'], $file);
            
            return $image;
        }else{
            return null;
        }
    }
}
