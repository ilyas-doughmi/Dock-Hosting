<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST["email"];
    $password = $_POST["password"];

    require_once("../php/connect.php");
    require_once("../Classes/Login.php");    

    $login = new Login($email,$password);
}