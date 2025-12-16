<?php

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $data = $_POST['data'];
    // Append to result.txt to preserve all logs
    file_put_contents("result.txt", $data, FILE_APPEND);
}

?>