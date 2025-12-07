<?php
    session_start();

    
    require_once "../php/connect.php";
    require_once "../Classes/Project.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $container_name = $_POST["container_name"];
    $project = new Project;
    $stop = $project->stopContainer($container_name);

}