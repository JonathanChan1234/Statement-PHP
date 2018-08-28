
<?php
    
    require_once 'user.php';
    
    $username = "";
    
    $password = "";
    
    $email = "";

    $newPassword = "";

    $image = "";
    
    if(isset($_POST['username'])){  
        $username = $_POST['username'];
    }
    
    if(isset($_POST['password'])){
        $password = $_POST['password'];
    }
    
    if(isset($_POST['email'])){
        $email = $_POST['email'];
    }

    if(isset($_POST['image'])) {
        $image = $_POST['image'];
    }
    
    $userObject = new User();
    
    // Registration
    
    if(!empty($username) && !empty($password) && !empty($email)){
        $hashed_password = md5($password);
        $json_registration = $userObject->createNewRegisterUser($username, $hashed_password, $email, $image);
        echo json_encode($json_registration);  
    }
    
    // Login
    
    if(!empty($username) && !empty($password) && empty($email)){
        $hashed_password = md5($password);
        $json_array = $userObject->loginUsers($username, $hashed_password);
        echo json_encode($json_array);
    }

    // //Change password

    // if(!empty($username) && !empty($password) && !empty($newPassword)) {
    //     $hashed_password = md5($password);
    //     $hashed_newPassword = md5($newPassword);    
    //     $json_array = $userObject->changePassword($username, $hashed_password, $hashed_newPassword);
    //     echo json_encode($json_array);
    // }
    ?>
