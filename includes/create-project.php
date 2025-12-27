<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once "../php/connect.php";
    require_once "../Classes/Project.php";

    $project_name = $_POST["project_name"];
    $project_name = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(" ", "-", $project_name));

    $base_path = dirname(__DIR__) . "/users/Projects/";
    $path = $base_path . $_SESSION["id"] . "/" . $project_name . "/";

    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true)) {
            header("location: ../pages/create-project.php?msg=Failed to create project directory&type=error");
            exit;
        }
    }

    // Create default index.php
    $default_content = "<?php\n\necho '<h1>Welcome to " . htmlspecialchars($project_name) . "</h1>';\necho '<p>This project was created with generic PHP support.</p>';\n";
    file_put_contents($path . "index.php", $default_content);

    // free port
    $Projects = New Project();
    $last_port = $Projects->trackPort();

    if ($last_port) {
        $last_port += 1;
    } else {
        $last_port = 8000;
    }

    // creating project in DB
    $create = $Projects->createProject($project_name, $last_port, $project_name, $_SESSION["id"]);

    if(!$create){
        header("location: ../pages/create-project.php?msg=Failed to create project in database&type=error");
        exit;
    }
    else{
                
        $host_base = getenv('HOST_BASE_PATH');
        if (!$host_base) {
             $host_base = getcwd() . "/../users"; 
        }
        
        $host_project_path = $host_base . "/Projects/" . $_SESSION["id"] . "/" . $project_name;

            $cmd = "docker run -d -p " .$last_port.":80 --name ".$project_name." -v \"".$host_project_path."\":/var/www/html php:8.2-apache";
        shell_exec($cmd);
        
        header("location: ../pages/dashboard.php?msg=Project created successfully!");
        exit();
    }
}
