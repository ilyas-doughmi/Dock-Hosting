<?php
    session_start();

    
    require_once "../../php/connect.php";
    require_once "../../Classes/Project.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $container_name = $_POST["container_name"];
    $project = new Project;
    $restart = $project->restartContainer($container_name);

      if($restart){
        // Redirect back to the project page instead of dashboard
        if(isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("location: ../../pages/project.php?container=" . $container_name);
        }
        exit();
    }
    else{
        echo "problem";
    }
}
