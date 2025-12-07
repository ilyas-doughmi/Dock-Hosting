<?php 

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $project_name = $_POST["project_name"];
    $file = $_FILES["files"];
}