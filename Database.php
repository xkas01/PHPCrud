<?php

class Database
{
    private string $db_host = "localhost";
    private string $db_user = "root";
    private string $db_pass = "root";
    private string $db_name = "crud";

    private $mysqli = "";
    private array $result = [];
    private bool $conn = false;

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
    public function insert($table, $params = []): bool
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
    public function select($table, $rows = "*", $join = null, $where = null, $order = null, $limit = null): bool
    {
        if ($this->tableExists($table)) {
            $sql = "select $rows from $table";
            if ($join != null) {
                $sql .= " join $join";
            }
            if ($where != null) {
                $sql .= " where $where";
            }
            if ($order != null) {
                $sql .= " order by $order";
            }
            if ($limit != null) {

                /*$page = $_GET['page'] ?? 1;*/
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                } else {
                    $page = 1;
                }

                $start = ($page - 1) * $limit;
                $sql .= " limit $start,$limit";
            }

            $query = $this->mysqli->query($sql);

            if ($query) {
                $this->result = $query->fetch_all(MYSQLI_ASSOC);
                return true;
            } else {
                array_push($this->result, $this->mysqli->error);
                return false;
            }

        } else {
            return false;
        }
    }

    //Function to Pagination from the database
    public function pagination($table, $join = null, $where = null, $limit = null)
    {
        if ($this->tableExists($table)) {
            if ($limit != null) {
                $sql = "select count(*) from $table";
                if ($join != null) {
                    $sql .= " join $join";
                }
                if ($where != null) {
                    $sql .= " where $where";
                }
                $query = $this->mysqli->query($sql);

                $total_record = $query->fetch_array();
                $total_record = $total_record[0];

                $total_page = ceil($total_record / $limit);

                $url = basename($_SERVER['PHP_SELF']);

                /*$page = $_GET['page'] ?? 1;*/
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                } else {
                    $page = 1;
                }

                $output = "<ul class='pagination'>";

                if ($page > 1) {
                    $output .= "<li><a href='$url?page=" . ($page - 1) . "'>Prev</a></li>";
                }

                if ($total_record > $limit) {
                    for ($i = 1; $i <= $total_page; $i++) {
                        if ($i == $page) {
                            $cls = "class='active'";
                        } else {
                            $cls = "";
                        }
                        $output .= "<li><a $cls href='$url?page=$i'>$i</a></li>";
                    }
                }
                if ($total_page > $page) {
                    $output .= "<li><a href='$url?page=" . ($page + 1) . "'>Next</a></li>";
                }
                $output .= "</ul>";
                return $output;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //Function to SQL from the database
    public function sql($sql): bool
    {
        $query = $this->mysqli->query($sql);

        if ($query) {
            $this->result = $query->fetch_all(MYSQLI_ASSOC);
            return true;
        } else {
            array_push($this->result, $this->mysqli->error);
            return false;
        }
    }

    //Function to SHOW from the database
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

    //Function getResult
    public function getResult(): array
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