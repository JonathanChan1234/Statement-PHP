<?php
    
    require_once 'account.php';
    
    $request = "";
    
    if(isset($_POST['request'])){  
        $request = $_POST['request'];
    }
      
    $accountObject = new Account();

    if(!empty($request)) {
        $decoded_request = json_decode($request);
        $result = $accountObject->deleteRecord($decoded_request);
        echo json_encode($result);
    }   
?>
