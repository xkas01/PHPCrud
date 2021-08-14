<?php

class Database
{
    private $db_host = "localhost";
    private $db_user = "root";
    private $db_pass = "root";
    private $db_name = "crud";

    private $mysqli = "";
    private $result = [];
    private $conn = false;

    public function __construct()
    {
        if (!$this->conn) {
            $this->mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
            $this->conn = true;
            if ($this->mysqli->connect_error) {
                array_push($this->result, $this->mysqli->connect_error);
                return false;
            }
        } else {
            return true;
        }
    }

    //Function to insert into the database
    public function insert()
    {

    }

    //Function to update row in database
    public function update()
    {

    }

    //Function to delete table or row(s) from database
    public function delete()
    {

    }

    //Function to SELECT from the database
    public function select()
    {

    }

    //close connection
    public function __destruct()
    {
        if ($this->conn) {
            if ($this->mysqli->close()) {
                $this->conn = false;
                return true;
            }
        } else {
            return false;
        }
    }
}