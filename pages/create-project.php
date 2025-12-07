<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
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
    <form action="../includes/create-project.php" method="POST" enctype="multipart/form-data">
        <input type="text" placeholder="project name" name="project_name">
        <input type="file" accept=".zip" name="files">
        <button>submit</button>
    </form>
</body>
</html>