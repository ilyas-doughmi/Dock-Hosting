<?php 

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];


    require_once("../php/connect.php");
    require_once("../Classes/Signup.php");


    $signup = new Signup($username,$email,$password);
}