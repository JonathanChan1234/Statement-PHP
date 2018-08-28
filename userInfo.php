
<?php
    
    require_once 'user.php';
    
    $username = "";
    
    if(isset($_POST['username'])){  
        $username = $_POST['username'];
    }
    $userObject = new User();
    
    if(!empty($username)){
        $json_array = $userObject->getUserInfo($username);
        echo json_encode($json_array);
    }
    ?>
