
<?php
    
    include_once 'db-connect.php';
    
    class User{
        
        private $db;
        
        private $db_table = "users";
        
        public function __construct(){
            $this->db = new DbConnect();
        }
        
        //check whether the account is valid
        public function isLoginExist($username, $password){
            
            $query = "select * from ".$this->db_table." where username = '$username' AND password = '$password' Limit 1"; 
            $result = mysqli_query($this->db->getDb(), $query);
            
            if(mysqli_num_rows($result) > 0){
                mysqli_close($this->db->getDb());
                return true;       
            }
            
            mysqli_close($this->db->getDb());
            
            return false;
            
        }
        
        public function isEmailUsernameExist($username, $email){
            
            $query = "select * from ".$this->db_table." where username = '$username' AND email = '$email'";
            $result = mysqli_query($this->db->getDb(), $query);

            if(mysqli_num_rows($result) > 0){
                return true;
            }   
            return false;
        }
        
        public function isValidEmail($email){
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }
        
        public function createNewRegisterUser($username, $password, $email, $image){
            $json = array();  
            $isExisting = $this->isEmailUsernameExist($username, $email);

            if($isExisting) {         
                $json['success'] = 0;
                $json['message'] = "Error in registering. Probably the username/email already exists";
            }
            else {   
                $isValid = $this->isValidEmail($email);
                if($isValid) {
                    $query = "insert into ".$this->db_table." (username, password, email, created_at, updated_at) values ('$username', '$password', '$email', NOW(), NOW())";
                    $inserted = mysqli_query($this->db->getDb(), $query);
                
                    if($inserted == 1) {
                        $query_create = "create table `$username` (".
                            "id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,".
                            "purpose TEXT NOT NULL,".
                            "description TEXT NOT NULL,".
                            "type VARCHAR(40) NOT NULL,".
                            "amount DOUBLE NOT NULL,".
                            "created_at DATETIME NOT NULL,".
                            "updated_at DATETIME DEFAULT NULL)";
                        $created = mysqli_query($this->db->getDb(), $query_create);
                        if($created == 1) {
                            $userid = $this->findUserId($username, $email);
                            if($userid !== 0) {
                                $json['success'] = 1;
                                $json['message'] = "Successfully registered the user and create user's database (id not found)";
                                if(!empty($image)) {
                                    if($this->setProfileImage($userid, $image)) {
                                        $json['success'] = 1;
                                        $json['message'] = "Successfully registered the user and create user's database (id found and photo uploaded)";
                                    }
                                    else {
                                        $json['success'] = 1;
                                        $json['message'] = "Successfully registered the user and create user's database (id found and photo NOT uploaded)";
                                    }
                                }
                            }
                            else {
                                $json['success'] = 1;
                                $json['message'] = "Successfully registered the user and create user's database";
                            }
                        }
                        else {
                            $json['success'] = 1;
                            $json['message'] = "Error: ".mysqli_error($this->db->getDb());
                        }
                    } 
                    else{
                        $json['success'] = 0;
                        $json['message'] = "Error in registering. Probably the username/email already exists";
                    }
                }
                else {
                    $json['success'] = 0;
                    $json['message'] = "Error in registering. Email Address is not valid";
                }
            }
            mysqli_close($this->db->getDb());
            return $json;
        }

        public function findUserId($username, $email) {
            $query = "select * from ".$this->db_table." where username = '$username' AND email = '$email'";
            $result = mysqli_query($this->db->getDb(), $query);
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id'];
                    $insert_query = "insert into profileimg (userid, status) values(". $id .", 0)";
                    $inserted = mysqli_query($this->db->getDb(), $insert_query);
                    if($inserted == 1) return $id;
                    else return 0;
                }
            }
            return 0;
        }

        private function setProfileImage($uid, $image) {
            if(!empty($image)) {
                $decoded_image = json_decode($image, true);
                $filename = "profile_".$uid;
                $uploaded = $this->uploadImage($decoded_image, $filename);
                if($uploaded !== false) {
                    $this->updateProfileStatus($uid);
                    return true;
                }
                return false;
            }
            return false;
        }

        private function updateProfileStatus($uid) {
            $query = "update profileimg set status = 1 where userid = ".$uid;
            $updated = mysqli_query($this->db->getDb(), $query);
        }

        private function uploadImage($file, $filename) {
            try {
                $extension = $this->getExtension($file['type']);
                $filename = $filename. ".". $extension; 
                if($extension == "") {
                    $extension = 'jpeg';
                }
                $file['data'] = str_replace(" ", "+", $file['data']);
                $binarydata = base64_decode($file['data']);
                $result = file_put_contents("upload/".$filename, $binarydata);
                if($result) {
                    @chmod($filename, 0777);
                    return $filename;
                } else {
                    return false;
                }
                return false;
            } 
            catch (Exception $e) {
                return false;
            }
        }

        private function getExtension($type) {
            $type_array = explode("?", $type);
            $type = trim($type_array[0]);
            $type = str_replace("\/", "/", $type);
            if ($type == "image/gif") {
                return 'gif';
            }
            if($type == "image/jpeg") {
                return 'jpeg';
            }
            if($type == "image/jpg") {
                return 'jpg';
            }
            if($type == "image/pjpeg") {
                return 'pjpeg';
            }
            if($type == "image/x-png") {
                return 'x-png';
            }
            if($type == "image/png") {
                return 'png';
            }
            if($type == "application/octet-stream") {
                return 'png';
            }
            if($type == "image/tiff") {
                return 'tiff';
            }
            if($type == "image/cms") {
                return 'cms';
            }
        }
        
        public function loginUsers($username, $password){   
            $json = array();
            $canUserLogin = $this->isLoginExist($username, $password);
            
            if($canUserLogin){
                $json['success'] = 1;
                $json['message'] = "Successfully logged in";
            }else{
                $json['success'] = 0;
                $json['message'] = "Incorrect details";
            }
            return $json;
        }

        public function changePassword($username, $oldPassword, $newPassword) {
            $json = array();
            $query = "update ".$this->db_table." set password = '$newPassword', updated_at = NOW()"." where username = '$username' AND password = '$oldPassword'";
            $update = mysqli_query($this->db->getDb(), $query);
            if($update == 1) {
                $json['message'] = "update sucessfully";
                $json['success'] = 1;
            }
            else {
                $json['message'] = "fail to change password";
                $json['success'] = 0;
            }
            mysqli_close($this->db->getDb());
            return $json;
        }

        public function getUserInfo($username) {
            $json = array();
            $query = "select * from ".$this->db_table." where username = '$username' Limit 1";
            $result = mysqli_query($this->db->getDb(), $query);
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_array($result);
                $json['message'] = "Success";
                $json['success'] = 1;
                $json['username'] = $row['username'];
                $json['created_at'] = $row['created_at'];
                $json['updated_at'] = $row['updated_at'];
                $json['email'] = $row['email'];
                if(checkProfileImageStatus($row[id])){
                    $json['image'] = "http://192.168.86.120/test-db-connection/image/profile_".$row[id].".jpg";
                }
                mysqli_close($this->db->getDb());
                return $json;
            }
            mysqli_close($this->db->getDb());
            $json['message'] = "fail to find data";
            $json['success'] = 0;
            return $json;
        }

        private function checkProfileImageStatus($uid) {
            $query = "select * from profileimg where userid = " .$uid;
            $result = mysqli_query($query);
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_array($result);
                if($row['status'] === 1) return true;
                return false;
            }
            return false;
        }
    }
    ?>
