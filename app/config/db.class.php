<?php

class Database {

    private $host = "localhost";
    private $dbName = "news_posts";
    private $user = "root";
    private $password = "";
    private $connection;

    public function connect(){
        // try {
        //     $this->connection = new PDO('mysqli:host=' . $this->host . ';dbname=' . $this->dbName,
        //                     $this->user, $this->password);
        //     $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // } catch (PDOException $e) {
        //     echo "Connection DB error: " . $e->getMessage();
        // }
        // return $this->connection;
        
        $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->dbName);
        if(!$this->connection){
            die("Failed to connect!");
        }
        return $this->connection;
    }
}