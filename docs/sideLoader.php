<?php

class sideLoader {
    public $conn;
    public $db;
    public $fullArray = [];

    function __construct($conn, $db) {
        $this->conn = $conn;
        $this->db = $db;
    }

    function load($root, $mode = 0) {
        $parentsArray = $this->getChildren(0);
        return $parentsArray;
    }

    function getChildren($id) {
        $query = "SELECT a.*
        FROM $this->db a
        LEFT OUTER JOIN $this->db b
            ON a.id = b.id AND a.v < b.v
        WHERE b.id IS NULL AND a.root = $id  ORDER BY importance DESC LIMIT 0, 22";
        if ($result = $this->conn->query($query)){
            if (mysqli_num_rows($result)==0){
                return;
            }
            else {
                $resultArray = [];
                while ($row = $result->fetch_assoc()){
                    $artArray = [];
                    $artArray["shortName"] = $row["shortName"];
                    $artArray["id"] = $row["id"];
                    $artArray["children"] = $this->getChildren($row["id"]);
                    $resultArray[] = $artArray;
                }
                return $resultArray;
            }
        }
    }
}


?>