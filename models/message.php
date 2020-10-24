<?php
require_once 'user.php';

class message{
    
    public $id;
    public $user_id;
    public $title;
    public $body;
    public $image;
    public $created_at;
    
    public function __construct($user_id="", $title="", $body="", $image=""){
        $this->user_id = $user_id;
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
    }
    
    public function get_user_name(){
        
    }
}
?>