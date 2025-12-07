<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once "../php/connect.php";
    require_once "../Classes/Project.php";

    $project_name = $_POST["project_name"];
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
        echo "find already port incremenet by 1";
        $last_port += 1;
    } else {
        echo "finding nothing";
        $last_port = 8000;
    }
    
    echo $last_port;
}
