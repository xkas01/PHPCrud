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
    public function insert($table, $params = [])
    {
        if ($this->tableExists($table)) {

            $table_columns = implode(', ', array_keys($params));
            $table_value = implode("', '", $params);

            $sql = "insert into $table ($table_columns) values ('$table_value')";
            // Make the query to insert to the database
            if ($this->mysqli->query($sql)) {
                array_push($this->result, $this->mysqli->insert_id);
                return true; // The data has been inserted
            } else {
                array_push($this->result, $this->mysqli->error);
                return false; // The data has not been inserted
            }
        } else {
            return false; // Table does not exist
        }
    }

    //Function to update row in database
    public function update($table, $params = [], $where = null)
    {
        if ($this->tableExists($table)) {
            $args = [];
            foreach ($params as $key => $value) {
                $args[] = "$key = '$value'";
            }
            $sql = "update $table set " . implode(', ', $args);
            if ($where != null) {
                $sql .= " where $where";
            }
            if ($this->mysqli->query($sql)) {
                array_push($this->result, $this->mysqli->affected_rows);
            } else {
                array_push($this->result, $this->mysqli->error);
            }
        } else {
            return false;
        }
    }

    //Function to delete table or row(s) from database
    public function delete($table, $where = null)
    {
        if ($this->tableExists($table)) {
            $sql = "delete from $table";
            if ($where != null) {
                $sql .= " where $where";
            }

            if ($this->mysqli->query($sql)) {
                array_push($this->result, $this->mysqli->affected_rows);
            } else {
                array_push($this->result, $this->mysqli->error);
            }

        } else {
            return false;
        }
    }

    //Function to SELECT from the database
    public function select()
    {

    }

    private function tableExists($table)
    {
        $sql = "show tables from $this->db_name like '$table'";
        $tableInDb = $this->mysqli->query($sql);
        if ($tableInDb) {
            if ($tableInDb->num_rows == 1) {
                return true;
            } else {
                array_push($this->result, $table . " does not exist in this database");
                return false;
            }
        }
    }

    public function getResult()
    {
        $val = $this->result;
        $this->result = [];
        return $val;
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