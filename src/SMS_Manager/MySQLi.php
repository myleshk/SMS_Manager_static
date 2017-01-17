<?php
/**
 * Created by PhpStorm.
 * User: myles
 * Date: 17/1/2017
 * Time: 10:36 PM
 */

namespace SMS_Manager;


class MySQLi
{

    private $conn;

    function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "123453421";
        $database = "SMS_Manager";

        // Create connection
        $this->conn = new \mysqli($servername, $username, $password, $database);
        $this->conn->set_charset("utf8");
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function escape($string)
    {
        return $this->conn->escape_string($string);
    }

    public function query($sql)
    {
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        } else {
            return [];
        }
    }

    public function insert($sql)
    {

        if ($this->conn->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \mysqli
     */
    public function getConn()
    {
        return $this->conn;
    }
}