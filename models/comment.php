<?php
class comment{
    
    public $id;
    public $message_id;
    public $name;
    public $content;
    public $created_at;
    
    public function __construct($message_id="", $name="", $content=""){
        $this->message_id = $message_id;
        $this->name = $name;
        $this->content = $content;
    }
}
?>