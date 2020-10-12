<?php
// 外部ファイルの読み込み
require_once 'config/const.php';
require_once 'models/message.php';

// データベースとやり取りを行う便利なクラス
class message_util{
    
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
    public function get_all_messages(){
        $pdo = $this->get_connection();
        $stmt = $pdo->query('SELECT * FROM messages ORDER BY id DESC');
        // フェッチの結果を、messageクラスのインスタンスにマッピングする
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'message');
        $messages = $stmt->fetchAll();
        $this->close_connection($pdo, $stmp);
        // メッセージクラスのインスタンスの配列を返す
        return $messages;
    }
    
    // id値からデータを抜き出すメソッド
    public function get_message_by_id($id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM messages WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'message');
        $message = $stmt->fetch();
        $this->close_connection($pdo, $stmp);
        // メッセージクラスのインスタンスを返す
        return $message;
    }
    
    // 画像ファイル名を取得するメソッド（uploadフォルダ内のファイルを物理削除するため）
    public function get_image_name_by_id($id){
        $pdo = $this->get_connection();
        $stmt = $pdo->prepare('SELECT * FROM messages WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'message');
        $message = $stmt->fetch();

        $this->close_connection($pdo, $stmp);
        
        return $message->image;
    }
    
    // データを1件登録するメソッド
    public function insert($message){
        $pdo = $this->get_connection();
        $stmt = $pdo -> prepare("INSERT INTO messages (name, title, body, image, password) VALUES (:name, :title, :body, :image, :password)");
        // バインド処理
        $stmt->bindParam(':name', $message->name, PDO::PARAM_STR);
        $stmt->bindParam(':title', $message->title, PDO::PARAM_STR);
        $stmt->bindParam(':body', $message->body, PDO::PARAM_STR);
        $stmt->bindParam(':image', $message->image, PDO::PARAM_STR);
        $stmt->bindParam(':password', $message->password, PDO::PARAM_STR);
        $stmt->execute();
        $this->close_connection($pdo, $stmp);
        
    }
    
    
    // データを更新するメソッド
    public function update($id, $message){
        $pdo = $this->get_connection();
        $image = $this->get_image_name_by_id($id);
        $stmt = $pdo->prepare('UPDATE messages SET name=:name, title=:title, body=:body, image=:image WHERE id = :id');
                        
        $stmt->bindParam(':name', $message->name, PDO::PARAM_STR);
        $stmt->bindParam(':title', $message->title, PDO::PARAM_STR);
        $stmt->bindParam(':body', $message->body, PDO::PARAM_STR);
        $stmt->bindParam(':image', $message->image, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();
        $this->close_connection($pdo, $stmp);
        
        // 画像の物理削除
        if($image !== $message->image){
            unlink(IMAGE_DIR . $image);
        }
    }
    
    // データを削除するメソッド
    public function delete($id){
        $pdo = $this->get_connection();
        $image = $this->get_image_name_by_id($id);
        
        $stmt = $pdo->prepare('DELETE FROM messages WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();
        $this->close_connection($pdo, $stmp);
        
        unlink(IMAGE_DIR . $message['image']);

    }
    
    // ファイルをアップロードするメソッド
    public function upload(){
        // ファイルを選択していれば
        if (!empty($_FILES['image']['name'])) {
            // ファイル名をユニーク化
            $image = uniqid(mt_rand(), true); 
            // アップロードされたファイルの拡張子を取得
            $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
            $file = IMAGE_DIR . $image;
        
            // uploadディレクトリにファイル保存
            move_uploaded_file($_FILES['image']['tmp_name'], $file);
            
            return $image;
        }else{
            return null;
        }
    }
}
