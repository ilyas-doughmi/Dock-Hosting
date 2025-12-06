<?php

    Class Login extends db{
        private   $email;
        private $password;

        public function __construct($email,$password)
        {
            $this->email = $email;
            $this->password = $password;
            $this->login_user();
        }
        
        private function login_user(){
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt =  $this->connect()->prepare($query);
            $stmt-> bindParam(":email",$this->email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user && password_verify($this->password,$user["password"])){
                echo "good";
            }
            else{
                echo "problem";
            }
        }
    } 


