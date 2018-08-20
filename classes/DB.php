<?php

class DB{
    private $conn;
    function __construct(){
        include('dbDetails.php');        
        $conn = new mysqli($conn_ip,$conn_username,$conn_password,$db_name);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $conn->query("SET NAMES 'utf8'");
        $this->conn=$conn;
    }
    function __destruct(){
        $this->conn->close();
    }
    function getConn(){
        return $this->conn;
    }
    function query($sql){
        $conn = $this->conn;
        if($result = $conn->query($sql)){
            if (strpos(strtoupper($sql), 'INSERT') !== false) {
                return $conn->insert_id;
            } else {
                return $result;
            }
        } else {
            echo $conn->error;
            return false;
        }
    }
    function initialize(){
        $conn = $this->conn;
        include ('./server/createDB/initialize.php');
    }
}

?>
