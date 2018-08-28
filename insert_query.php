
<?php
    
    require_once 'account.php';
    
    $username = "";
    
    $purpose = "";
    
    $description = "";

    $type = "";

    $amount = 0.0;
    
    if(isset($_POST['username'])){  
        $username = $_POST['username'];
    }
    
    if(isset($_POST['purpose'])){
        $purpose = $_POST['purpose'];
    }

    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    
    if(isset($_POST['description'])){
        $description = $_POST['description'];
    }

    if(isset($_POST['amount'])) {
        $amount = $_POST['amount'];
        $amount = doubleval($amount);
    }
    
    $accountObject = new Account();

    if(!empty($username) && !empty($purpose) && !empty($type) && !empty($description) && !empty($amount)) {
        $result = $accountObject->addQuery($username, $purpose, $description, $type, $amount);
        echo json_encode($result);
    }
    ?>
