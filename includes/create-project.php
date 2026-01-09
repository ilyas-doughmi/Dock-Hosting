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
        $project_name_input = trim($_POST["project_name"]);
        if (empty($project_name_input)) {
            header("location: ../pages/create-project.php?msg=Project name cannot be empty&type=error");
            exit;
        }
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
        
        $safe_branch = escapeshellarg($branch);
        $safe_clone_url = escapeshellarg($clone_url);
        $safe_path = escapeshellarg($path);
        
        $cmd = "git clone -b {$safe_branch} {$safe_clone_url} {$safe_path} 2>&1";
        exec($cmd, $output, $return_var);


        if ($return_var !== 0) {
            rmdir($path);
            header("location: ../pages/create-project.php?msg=Failed to clone repository.&type=error");
            exit;
        }

        $cloned_items = array_diff(scandir($path), array('.', '..', '.git'));
        if (count($cloned_items) === 1) {
            $only_item = reset($cloned_items);
            $full_item_path = $path . $only_item;
            
            if (is_dir($full_item_path)) {
                $inner_files = array_diff(scandir($full_item_path), array('.', '..'));
                foreach ($inner_files as $file) {
                    rename($full_item_path . "/" . $file, $path . $file);
                }
                rmdir($full_item_path);
            }
        }

    } else {
        $default_content = "<?php\n\necho '<h1>" . htmlspecialchars($project_name) . "</h1>';\n";
        file_put_contents($path . "index.php", $default_content);
    }

    $repo_name = isset($_POST['github_repo']) ? basename($_POST['github_repo']) : '';

    $htaccess_content = "ErrorDocument 500 \"Internal Server Error\"\n";
    $htaccess_content .= "php_flag display_errors off\n";
    $htaccess_content .= "php_flag log_errors on\n";
    $htaccess_content .= "php_value error_log /var/www/html/error.log\n";

    if (!file_exists($path . ".htaccess")) {
        file_put_contents($path . ".htaccess", $htaccess_content);
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
         if (isset($_ENV['HOST_BASE_PATH']) && !empty($_ENV['HOST_BASE_PATH'])) {
            $host_base = $_ENV['HOST_BASE_PATH'];
        } elseif (getenv('HOST_BASE_PATH')) {
            $host_base = getenv('HOST_BASE_PATH');
        } else {
             $host_base = '/home/deployer/dock-hosting-data'; 
        }
        
        $host_project_path = $host_base . "/Projects/" . $_SESSION["id"] . "/" . $project_name;

        $subdomain = $project_name . ".dockhosting.dev";
        
        $safe_port = escapeshellarg($last_port . ":80");
        $safe_name = escapeshellarg($project_name);
        $safe_subdomain = escapeshellarg($subdomain);
        $safe_volume = escapeshellarg($host_project_path . ":/var/www/html");
        
        $cmd = "docker run -d -p {$safe_port} --name {$safe_name} --network proxy_network -e VIRTUAL_HOST={$safe_subdomain} -e LETSENCRYPT_HOST={$safe_subdomain} -v {$safe_volume} dock-hosting-user";
        
        shell_exec($cmd);
        
        require_once __DIR__ . '/Logger.php';
        $logger = new Logger();
        $logger->logActivity($_SESSION["id"], 'CREATE_PROJECT', "Created project: $project_name");

        header("location: ../pages/dashboard.php?msg=Project created successfully");
        exit();
    }
}
