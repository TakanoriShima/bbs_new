<?php
class user{
    
    public $id;
    public $name;
    public $nickname;
    public $email;
    public $avatar;
    public $password;
    public $created_at;
    
    public function __construct($name="", $nickname="", $email="", $avatar="", $password=""){
        $this->name = $name;
        $this->nickname = $nickname;
        $this->email = $email;
        $this->avatar = $avatar;
        $this->password = $password;
    }
}
?>