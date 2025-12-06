<?php
    session_start();
    
    if(!isset($_SESSION["id"])){
        header("location: ../index.php");
        exit;
    }
    require_once("../php/connect.php");
    require_once("../Classes/Project.php");

    $projects = new Project();
    $user_Projects = $projects->getProjects($_SESSION["id"]);

?>