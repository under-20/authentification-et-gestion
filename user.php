<?php
session_start();
require_once 'core/conn.php';

class Users
{
    public $id_user;
    public $username;
    public $email;
    public $mdps;
    public $sexe;
    public $role;
    public $avatar;

    public function __construct($username = null, $email = null, $mdps = null, $sexe = null, $role = null, $avatar = 'default_avatar.png', $id_user = null)
    {
        $this->id_user = $id_user;
        $this->username = $username;
        $this->email = $email;
        $this->mdps = $mdps;
        $this->sexe = $sexe;
        $this->role = $role;
        $this->avatar = $avatar;
    }
}
?>