<?php
session_start();
   require_once "../../php/connect.php";
    require_once "../../Classes/Project.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $container = $_POST["container_name"];
    $project_name = $_POST["project_name"];
    $path = "C:/xampp/htdocs/Dock-Hosting/users/Projects/" . $_SESSION["id"] . "/" . $project_name . "/";

    $project = new Project;

    if (is_dir($path)) {
        $delete = $project->deleteProject($container,$path);
         header("location: ../../pages/dashboard.php");
            exit();

    }
}
