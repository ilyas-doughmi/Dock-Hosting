<?php

class Signup extends db
{
    private $username;
    private $email;
    private $password;


    public function __construct($username, $email, $password)
    {
        parent::__construct();
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
        try{
            $stmt->execute();
            header("location: ../login.php?msg=Account created successfully! Please login");
            exit;
        }catch(PDOException $e){
            if($e->getCode() == '23000'){
                header("location: ../login.php?msg=Email or username already exists&type=error");
                exit;
            }
        else{
            header("location: ../login.php?msg=Registration failed. Please try again&type=error");
            exit;
        }
        }
    }
}
