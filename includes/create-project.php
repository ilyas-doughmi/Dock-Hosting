<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once "../php/connect.php";
    require_once "../Classes/Project.php";
    require_once '../Classes/GitHubManager.php';

    $framework = $_POST['framework'] ?? 'php';
    $source_type = $_POST['source_type'] ?? 'empty';
    $project_name_input = "";

    if ($source_type === 'github') {
        $repo_full_name = $_POST['github_repo']; 
        $parts = explode('/', $repo_full_name);
        $project_name_input = end($parts);
        
        if (strlen($project_name_input) > 20) {
            $project_name_input = substr($project_name_input, 0, 20);
        }
    } else {
        $project_name_input = trim($_POST["project_name"]);
        
        if (empty($project_name_input)) {
            echo json_encode(['success' => false, 'error' => 'Project name cannot be empty']);
            exit;
        }
        
        if (strlen($project_name_input) > 20) {
            echo json_encode(['success' => false, 'error' => 'Project name too long (max 20 chars)']);
            exit;
        }
    }

    $clean_name = preg_replace('/[^a-zA-Z0-9-]/', '', str_replace(" ", "-", $project_name_input));
    $project_name = strtolower($clean_name . "-" . substr(bin2hex(random_bytes(4)), 0, 6));

    $base_path = dirname(__DIR__) . "/users/Projects/";
    $path = $base_path . $_SESSION["id"] . "/" . $project_name . "/";

    if (!is_dir($path)) {
        if (!mkdir($path, 0777, true)) {
            echo json_encode(['success' => false, 'error' => 'Failed to create project directory']);
            exit;
        }
    }

    if ($source_type === 'github') {
        $gh = new GitHubManager();
        $token = $gh->getAccessToken($_SESSION['id']);
        $repo_full_name = $_POST['github_repo'];
        $branch = $_POST['github_branch'] ?? 'main';

        if (!$token) {
            echo json_encode(['success' => false, 'error' => 'GitHub token not found']);
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
            echo json_encode(['success' => false, 'error' => 'Failed to clone repository', 'details' => implode("\n", $output)]);
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
        if ($framework === 'php') {
            $default_content = "<?php\n\necho '<h1>" . htmlspecialchars($project_name) . "</h1>';\n";
            file_put_contents($path . "index.php", $default_content);
        }
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


    
    $create = $Projects->createProject($project_name, $last_port, $project_name, $_SESSION["id"], $framework);

    if(!$create){
        echo json_encode(['success' => false, 'error' => 'Database error']);
        exit;
    }
    else{
         if (isset($_ENV['HOST_BASE_PATH']) && !empty($_ENV['HOST_BASE_PATH'])) {
            $host_base = $_ENV['HOST_BASE_PATH'];
        } elseif (getenv('HOST_BASE_PATH')) {
            $host_base = getenv('HOST_BASE_PATH');
        } else {
             // Fallback to local path if env not set (Fixes Windows/XAMPP volume issues)
             $host_base = str_replace('\\', '/', dirname(__DIR__) . '/users'); 
        }
        
        $host_project_path = $host_base . "/Projects/" . $_SESSION["id"] . "/" . $project_name;

        $subdomain = $project_name . ".dockhosting.dev";
        
        $safe_port = escapeshellarg($last_port);
        $safe_name = escapeshellarg($project_name);
        $safe_subdomain = escapeshellarg($subdomain);
        $safe_volume = escapeshellarg($host_project_path . ":/var/www/html");
        

        if ($framework === 'node') {

            $internal_port = "3000";
            $image = "node:18-alpine";

            if($source_type !== 'github' && !file_exists($path . "index.js")) {
                file_put_contents($path . "index.js", "const http = require('http');\n\nconst server = http.createServer((req, res) => {\n  res.statusCode = 200;\n  res.setHeader('Content-Type', 'text/plain');\n  res.end('Hello from Node.js (Hot Reload Active)!');\n});\n\nconst port = process.env.PORT || 3000;\nserver.listen(port, () => {\n  console.log(`Server running at http://localhost:\${port}/`);\n});");
                file_put_contents($path . "package.json", "{\n  \"name\": \"$project_name\",\n  \"version\": \"1.0.0\",\n  \"main\": \"index.js\",\n  \"scripts\": {\n    \"start\": \"node --watch index.js\"\n  }\n}");
            }
            // Use --watch flag for auto-reloading on file changes
            $command = "sh -c \"apk add --no-cache bash && cd /var/www/html && if [ -f package.json ]; then npm install; fi && if [ -f index.js ]; then node --watch index.js; else npm start; fi\"";
            
        } elseif ($framework === 'python') {
            // Python Logic
            if($source_type !== 'github' && !file_exists($path . "app.py")) {
                file_put_contents($path . "app.py", "from flask import Flask\napp = Flask(__name__)\n\n@app.route('/')\ndef hello():\n    return 'Hello from Python!'\n\nif __name__ == '__main__':\n    app.run(host='0.0.0.0', port=5000)");
                file_put_contents($path . "requirements.txt", "flask");
            }
            $command = "sh -c \"if [ -f requirements.txt ]; then pip install -r requirements.txt; fi && python app.py\"";

        } else {
            $internal_port = "80"; 
            $image = "dock-hosting-user"; 
            $command = ""; 
        }

        $safe_internal_port = escapeshellarg($internal_port);
        

        $cmd = "docker run -d -p {$safe_port}:{$internal_port} --name {$safe_name} --network proxy_network -e VIRTUAL_HOST={$safe_subdomain} -e LETSENCRYPT_HOST={$safe_subdomain} -e VIRTUAL_PORT={$internal_port} -e PORT={$internal_port} -v {$safe_volume}";
        

        
        $cmd .= " -e DB_HOST=dock-hosting-db -e DB_USER=root -e DB_PASSWORD=" . escapeshellarg(getenv('DB_PASSWORD')); 
        
        $cmd .= " {$image} {$command}";
        
        shell_exec($cmd);
        
        require_once __DIR__ . '/Logger.php';
        $logger = new Logger();
        $logger->logActivity($_SESSION["id"], 'CREATE_PROJECT', "Created ($framework) project: $project_name");

        echo json_encode(['success' => true, 'project_name' => $project_name]);
        exit();
    }
}
