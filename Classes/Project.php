<?php
Class Project extends db{
    private $project_id;
    private $user_id;
    private $project_name;
    private $port;
    private $container_name;
    private $status;


    public function getProjects($user_id){
        $query = "SELECT * FROM Project WHERE user_id =  :user_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(":user_id",$user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        return $stmt->execute();
    }
    
}

