<?php 

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $project_name = $_POST["project_name"];
    $file = $_FILES["files"];
    
    $path = "C:/xampp/htdocs/Dock-Hosting/users/Projects/";

    $path = $path . $file["name"];
    move_uploaded_file($file["tmp_name"],$path);
}