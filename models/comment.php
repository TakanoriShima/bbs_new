<?php
class comment{
    
    public $id;
    public $user_id;
    public $message_id;
    public $content;
    public $created_at;
    
    public function __construct($user_id="", $message_id="", $content=""){
        $this->user_id = $user_id;
        $this->message_id = $message_id;
        $this->content = $content;
    }
}
?>