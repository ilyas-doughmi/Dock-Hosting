<?php

class Signup extends db
{
    private $username;
    private $email;
    private $password;


    public function __construct($username, $email, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this-> password = password_hash($password,PASSWORD_DEFAULT);

        $this->inserting_user();
    }

    private function inserting_user()
    {
        $query = "INSERT INTO users(username,email,password) VALUES(:username, :email, :password)";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->execute();
    }
}
