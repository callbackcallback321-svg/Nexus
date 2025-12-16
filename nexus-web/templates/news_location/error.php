<?php

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $denied = $_POST['Denied'];
    $unavailable = $_POST['Una'];
    $timeout = $_POST['Time'];
    $unknown = $_POST['Unk'];
    
    $error_data = "Location Error: ";
    if($denied) $error_data .= $denied;
    if($unavailable) $error_data .= $unavailable;
    if($timeout) $error_data .= $timeout;
    if($unknown) $error_data .= $unknown;
    
    file_put_contents("result.txt", $error_data);
}

?>

