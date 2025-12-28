<?php
session_start();
   require_once "../../php/connect.php";
    require_once "../../Classes/Project.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $container = $_POST["container_name"];
    $project_name = $_POST["container_name"];
    $base_path = dirname(__DIR__, 2);
    $path = $base_path . "/users/Projects/" . $_SESSION["id"] . "/" . $project_name . "/";

    $project = new Project;

    if (is_dir($path)) {
        $delete = $project->deleteProject($container,$path);
         header("location: ../../pages/dashboard.php");
        exit();
    }
}
