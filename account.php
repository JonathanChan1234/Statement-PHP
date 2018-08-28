
<?php
    
    include_once 'db-connect.php';
    
    class Account{
        
        private $db;
        
        private $db_table = "users";
        
        public function __construct() {
            $this->db = new DbConnect();
        }

        public function addQuery($username, $purpose, $description, $type, $amount) {
            $json = array();
            $query = "insert into ".$username." (purpose, description, type, amount, created_at, updated_at) values ('$purpose', '$description', '$type', '$amount', NOW(), NOW())";
            $added = mysqli_query($this->db->getDb(), $query);
            if($added) {
                $json['success'] = 1;
                $json['message'] = "add successfully";
            }
            else {
                $json['success'] = 0;
                $json['message'] = "Error: ".mysqli_error($this->db->getDb());
            }
            mysqli_close($this->db->getDb());
            return $json;
        }

        public function updateQuery($username, $purpose, $description, $type, $amount, $created_at) {
            $json = array();
            $query = "update ".$username." set purpose = '$purpose', description = '$description', type = '$type', amount = '$amount', updated_at = NOW() where created_at = '$created_at'";
            $updated = mysqli_query($this->db->getDb(), $query);
            if($updated) {
                $json['success'] = 1;
                $json['message'] = "update successfully";
            }
            else {
                $json['success'] = 0;
                $json['message'] = "Error: ".mysqli_error($this->db->getDb());
            }
            mysqli_close($this->db->getDb());
            return $json;
        }

        public function selectById($username, $id) {
            $json = array();
            $query = "select ".$username. "where id = '$id'";

        }

        public function readRecord($username) {
            $json = array();
            $query = "select * from ".$username." order by created_at DESC";
            $selected = mysqli_query($this->db->getDb(), $query);
            if($selected == false) {
                $json['success'][0] = 0;
                $json['message'][0] = "Error: ".mysqli_error($this->db->getDb());
                mysqli_close($this->db->getDb());
                return $json;
            }
            $i = 0;
            if(mysqli_num_rows($selected) > 0) {
                while($row = mysqli_fetch_assoc($selected)) {
                    $json[] = $row;
                }
                mysqli_free_result($selected);
            }
            else {
                $json['success'][0] = 0;
                $json['message'][0] = "No result found";    
            }
            mysqli_close($this->db->getDb());
            return $json;
        }

        public function readRecordwithFilter($username, $category) {
            $json = array();
            $query = "select * from ".$username." where purpose = '$category' order by created_at DESC";
            $selected = mysqli_query($this->db->getDb(), $query);
            if($selected == false) {
                $json[0]['success'] = 0;
                $json[0]['message'] = "Error: ".mysqli_error($this->db->getDb());
                mysqli_close($this->db->getDb());
                return $json;
            }
            if(mysqli_num_rows($selected) > 0) {
                while($row = mysqli_fetch_assoc($selected)) {
                    $json[] = $row;
                }
                mysqli_free_result($selected);
            }
            else {
                $json[0]['success'] = 0;
                $json[0]['message'] = "No result found";    
            }
            mysqli_close($this->db->getDb());
            return $json;
        }

        public function deleteRecord($request) {
            $json = array();
            $ids = array();
            $ids = $request->ids;
            $username = $request->username;
            $query = "delete from ".$username." where id in (";
            for($i=0; $i<sizeof($ids); $i++) {
                if($i==0) {
                    $query = $query.$ids[$i];
                }
                else {
                    $query = $query.",".$ids[$i];
                }
            }
            $query = $query.")";
            $deleted = mysqli_query($this->db->getDb(), $query);
            if($deleted) {
                $json['success'] = 1;
                $json['message'] = "Delete successfully";
            }
            else {
                $json['success'] = 0;
                $json['message'] = "Something is wrong";
            }
            mysqli_close($this->db->getDb());
            return $json;
        }
    }
?>
