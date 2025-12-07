<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once "../php/connect.php";
    require_once "../Classes/Project.php";

    $project_name = $_POST["project_name"];
    $project_name = str_replace(" ","-",$project_name);
    $file = $_FILES["files"];
    $path = "C:/xampp/htdocs/Dock-Hosting/users/Projects/" . $_SESSION["id"] . "/" . $project_name . "/";

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    $file_dir = $path . $file["name"];
    move_uploaded_file($file["tmp_name"], $file_dir);

    // extract file from zip

    $extract = new ZipArchive;
    if ($extract->open($file_dir) === TRUE) {
        $extract->extractTo($path);
        $extract->close();
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
        echo "problem";
    }
    else{
        shell_exec("docker run -d -p " .$last_port.":80 --name ".$project_name." -v ".$path.":/var/www/html php:8.2-apache");
        header("location: ../pages/dashboard.php");
        exit();
    }

}
