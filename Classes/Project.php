<?php
Class Project extends db{
    
    public function getProjects($user_id){
        $query = "SELECT * FROM Project WHERE user_id =  :user_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(":user_id",$user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getContainersCount($user_id){
        $query = "SELECT COUNT(*) as count FROM Project WHERE user_id = :user_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(":user_id",$user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function trackPort(){
        $port_query = "SELECT port FROM Project ORDER BY port DESC limit 1;";
        $stmt = $this->connect()->prepare($port_query);
        $stmt->execute();  
        $result = $stmt->fetch(PDO::FETCH_ASSOC);  


        if(!$result){
            return false;
        }
        else{
            return $result["port"];
        }
    }

    public function createProject($project_name,$port,$container_name,$user_id){
        $create_query = "INSERT INTO Project(project_name,port,container_name,status,user_id) 
                        VALUES(:project_name,:port,:container_name,:status,:user_id)";

        $stmt = $this->connect()->prepare($create_query);
        $stmt->bindParam(":project_name",$project_name);
        $stmt->bindParam(":container_name",$container_name);
        $stmt->bindParam(":port",$port);
        $stmt->bindValue(":status","running");
        $stmt->bindParam(":user_id",$user_id);
        return $stmt->execute();
    }

    public function stopContainer($container_name){
        shell_exec("docker stop ".$container_name);
        $stop_query = "UPDATE Project SET status = 'stopped' WHERE user_id = :user_id AND container_name = :container_name";

        $stmt = $this->connect()->prepare($stop_query);
        $stmt->bindParam(":user_id",$_SESSION["id"]);
        $stmt->bindParam(":container_name",$container_name);
        $result = $stmt->execute();

        if($result){
            return $result;
        }
        else{
            return false;
        }
    }

    public function startContainer($container_name){
         shell_exec("docker start ".$container_name);
        $start_query = "UPDATE Project SET status = 'running' WHERE user_id = :user_id AND container_name = :container_name";

        $stmt = $this->connect()->prepare($start_query);
        $stmt->bindParam(":user_id",$_SESSION["id"]);
        $stmt->bindParam(":container_name",$container_name);
        $result = $stmt->execute();

        if($result){
            return $result;
        }
        else{
            return false;
        }
    }
    
    public function deleteProject($container_name,$file_dir){
        $this->removeContainer($container_name);
        $this->removeDir($file_dir);
        $this->removeDatabase($container_name);
    }

    private function removeContainer($container){
        shell_exec("docker rm -f ".$container);
    }
    private function removeDir($file_dir){
        shell_exec("rmdir /s /q " . $file_dir);
    }
    private function removeDatabase($container_name){
        $remove_query = "DELETE FROM Project WHERE user_id = :user_id AND container_name = :container_name";
        $stmt = $this->connect()->prepare($remove_query);
        $stmt->bindParam(":container_name",$container_name);
        $stmt->bindParam(":user_id",$_SESSION["id"]);
        $result = $stmt->execute();

        if($result){
            return $result;
        }
        else{
            return false;
        }
    }
    public function getProjectFiles($container_name){
        $path = "C:/xampp/htdocs/Dock-Hosting/users/Projects/" . $_SESSION["id"] . "/" . $container_name . "/";

        if(is_dir($path)){
            $files = scandir($path); 
            $files = array_diff($files,array('.','..'));
            return $files;
        }
        else{
            return [];
        }
    }

    public function getFileContent($container_name,$file_name){
        $path = "C:/xampp/htdocs/Dock-Hosting/users/Projects/" . $_SESSION["id"] . "/" . $container_name . "/" . $file_name ;

        if(is_file($path)){
            return file_get_contents($path);
        }
        else{
            return "file not found";
        } 
    }
}

