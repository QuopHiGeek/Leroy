<?php
class Database{

    // specify your own database credentials
    private $host = "localhost";
    private $db_name = "elabmess_vlis";
    private $username = 'elabmess_dbadmin';
    private $password = '$c^A}7.(^4nn';
    public $conn;
    
    

    // get the database connection
    public function getConnection(){

        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>