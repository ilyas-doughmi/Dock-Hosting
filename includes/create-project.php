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
            header("location: ../pages/create-project.php?msg=Failed to create project directory&type=error");
            exit;
        }
    }

    $file_dir = $path . $file["name"];
    if (!move_uploaded_file($file["tmp_name"], $file_dir)) {
        error_log("Upload failed: " . print_r(error_get_last(), true));
        header("location: ../pages/create-project.php?msg=Failed to upload file. Error code: " . $file["error"] . "&type=error");
        exit;
    }
    
    error_log("File uploaded to: " . $file_dir . " | Size: " . filesize($file_dir));

    // extract file from zip
    $extract = new ZipArchive;
    $res = $extract->open($file_dir);
    if ($res === TRUE) {
        $extract->extractTo($path);
        $extract->close();
        unlink($file_dir);
    } else {
        error_log("Zip extraction failed. Result code: " . $res . " File: " . $file_dir);
        header("location: ../pages/create-project.php?msg=Failed to extract ZIP file. Code: " . $res . "&type=error");
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
        header("location: ../pages/create-project.php?msg=Failed to create project in database&type=error");
        exit;
    }
    else{
        shell_exec("docker run -d -p " .$last_port.":80 --name ".$project_name." -v ".$path.":/var/www/html php:8.2-apache");
        header("location: ../pages/dashboard.php?msg=Project created successfully!");
        exit();
    }

}
