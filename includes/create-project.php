<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once "../php/connect.php";
    require_once "../Classes/Project.php";

    $project_name = $_POST["project_name"];
    $project_name = str_replace(" ","-",$project_name);
    $file = $_FILES["files"];
    
    $base_path = dirname(__DIR__) . "/users/Projects/";
    $path = $base_path . $_SESSION["id"] . "/" . $project_name . "/";

    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true)) {
            header("location: ../pages/create-project.php?msg=Failed to create project directory");
            exit;
        }
    }

    $file_dir = $path . $file["name"];
    if (!move_uploaded_file($file["tmp_name"], $file_dir)) {
        header("location: ../pages/create-project.php?msg=Failed to upload file");
        exit;
    }

    // extract file from zip
    $extract = new ZipArchive;
    if ($extract->open($file_dir) === TRUE) {
        $extract->extractTo($path);
        $extract->close();
        unlink($file_dir);
    } else {
        header("location: ../pages/create-project.php?msg=Failed to extract ZIP file");
        exit;
    }

    // free port
    $Projects = New Project();
    $last_port = $Projects->trackPort();

    if ($last_port) {
        $last_port += 1;
    } else {
        $last_port = 8000;
    }
    

    // creating project 
    $create = $Projects->createProject($project_name,$last_port,$project_name,$_SESSION["id"]);

    if(!$create){
        header("location: ../pages/create-project.php?msg=Failed to create project in database");
        exit;
    }
    else{
        shell_exec("docker run -d -p " .$last_port.":80 --name ".$project_name." -v ".$path.":/var/www/html php:8.2-apache");
        header("location: ../pages/dashboard.php?msg=Project created successfully!");
        exit();
    }

}
