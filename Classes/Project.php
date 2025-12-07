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
}

