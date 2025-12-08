<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}
require_once("../php/connect.php");
require_once("../Classes/Project.php");

$project = new Project;
$content = "";
if(isset($_GET["container"])){
    $container_name = $_GET["container"];
    $files = $project->getProjectFiles($container_name);
}
else{
    header("location: dashboard.php");
}

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $file_requested = $_GET["file"];
    $newcontent = $_POST["newcontent"];
    $save = $project->saveFileChanges($container_name,$file_requested,$newcontent);
}
if(isset($_GET["file"])){
    $file_requested = $_GET["file"];
    $content = $project->getFileContent($container_name,$file_requested);
}





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
        foreach($files as $fl){?>
            <a href="file-manager.php?container=<?= $container_name ?>&file=<?= $fl ?>">
             <h1><?= $fl ?></h1>
            </a>
   <?php } ?>

   <h1>script text</h1>
<form action="file-manager.php?container=<?= $container_name ?>&file=<?= $file_requested ?>" method="POST">
        <textarea name="newcontent" id=""><?=htmlspecialchars($content)?></textarea>
<button>save</button>
</form>

</body>
</html>