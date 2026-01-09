<?php
    require_once __DIR__ . '/../includes/traffic_middleware.php';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '.dockhosting.dev',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
    Class Login extends db{
        private   $email;
        private $password;

        public function __construct($email,$password)
        {
            parent::__construct();
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
                $_SESSION["id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"] ?? 'user'; 
                
                
                require_once __DIR__ . '/../includes/Logger.php';
                $logger = new Logger();
                $logger->logActivity($user['id'], 'LOGIN', 'User logged in successfully');

                if($_SESSION["role"] === 'admin') {
                     header("location: ../pages/admin/dashboard.php");
                } else {
                     header("location: ../pages/dashboard.php");
                }
                exit;
            }
            else{
                header("location: ../login.php?msg=Invalid email or password&type=error");
                exit;
            }
        }
    } 


