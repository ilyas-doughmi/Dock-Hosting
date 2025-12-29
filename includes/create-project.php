<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once "../php/connect.php";
    require_once "../Classes/Project.php";
    require_once '../Classes/GitHubManager.php';

    $source_type = $_POST['source_type'] ?? 'empty';
    $project_name_input = "";

    if ($source_type === 'github') {
        $repo_full_name = $_POST['github_repo']; 
        $parts = explode('/', $repo_full_name);
        $project_name_input = end($parts); 
    } else {
        $project_name_input = $_POST["project_name"];
    }

    $clean_name = preg_replace('/[^a-zA-Z0-9-]/', '', str_replace(" ", "-", $project_name_input));
    $project_name = strtolower($clean_name . "-" . substr(bin2hex(random_bytes(4)), 0, 6));

    $base_path = dirname(__DIR__) . "/users/Projects/";
    $path = $base_path . $_SESSION["id"] . "/" . $project_name . "/";

    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true)) {
            header("location: ../pages/create-project.php?msg=Failed to create project directory&type=error");
            exit;
        }
    }

    if ($source_type === 'github') {
        $gh = new GitHubManager();
        $token = $gh->getAccessToken($_SESSION['id']);
        $repo_full_name = $_POST['github_repo'];
        $branch = $_POST['github_branch'] ?? 'main';

        if (!$token) {
            header("location: ../pages/create-project.php?msg=GitHub token not found. Please reconnect.&type=error");
            exit;
        }

        $clone_url = "https://oauth2:{$token}@github.com/{$repo_full_name}.git";
        
        
        $cmd = "git clone -b {$branch} {$clone_url} {$path} 2>&1";
        exec($cmd, $output, $return_var);

        if ($return_var !== 0) {
            rmdir($path);
            header("location: ../pages/create-project.php?msg=Failed to clone repository.&type=error");
            exit;
        }

    } else {
        $default_content = "<?php\n\necho '<h1>" . htmlspecialchars($project_name) . "</h1>';\n";
        file_put_contents($path . "index.php", $default_content);
    }

    if (!file_exists($path . ".htaccess")) {
        $htaccess = "php_flag display_errors off\nphp_flag log_errors on\nphp_value error_log /var/www/html/error.log";
        file_put_contents($path . ".htaccess", $htaccess);
    }

    file_put_contents($path . "error.log", "");
    chmod($path . "error.log", 0666);

    $Projects = New Project();
    $last_port = $Projects->trackPort();

    if ($last_port) {
        $last_port += 1;
    } else {
        $last_port = 8000;
    }

    $create = $Projects->createProject($project_name, $last_port, $project_name, $_SESSION["id"]);

    if(!$create){
        header("location: ../pages/create-project.php?msg=Database error&type=error");
        exit;
    }
    else{
        $host_base = getenv('HOST_BASE_PATH');
        if (!$host_base) {
             $host_base = getcwd() . "/../users"; 
        }
        
        $host_project_path = $host_base . "/Projects/" . $_SESSION["id"] . "/" . $project_name;

        $subdomain = $project_name . ".dockhosting.dev";
        $cmd = "docker run -d -p " .$last_port.":80 --name ".$project_name." --network proxy_network -e VIRTUAL_HOST=".$subdomain." -e LETSENCRYPT_HOST=".$subdomain." -v \"".$host_project_path."\":/var/www/html php:8.2-apache";
        shell_exec($cmd);
        
        header("location: ../pages/dashboard.php?msg=Project created successfully");
        exit();
    }
}
