<?php
class message{
    
    public $id;
    public $name;
    public $title;
    public $body;
    public $image;
    public $password;
    public $created_at;
    
    public function __construct($name="", $title="", $body="", $image="", $password=""){
        $this->name = $name;
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
        $this->password = $password;
    }
}
?>