<?php
    
    require_once 'account.php';
    
    $username = "";
    $category = "";
    
    if(isset($_POST['username'])){  
        $username = $_POST['username'];
    }

    if(isset($_POST['category'])) {
        $category = $_POST['category'];
    }
      
    $accountObject = new Account();

    if(!empty($username) && empty($category)) {
        $result = $accountObject->readRecord($username);
        echo json_encode($result);
    }

    if(!empty($username) && !empty($category)) {
        if($category === "All") {
            $result = $accountObject->readRecord($username);
            echo json_encode($result);
        }
        else {
            $result = $accountObject->readRecordwithFilter($username, $category);
            echo json_encode($result);
        }   
    }
    ?>
