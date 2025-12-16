<?php

if(isset($_FILES['audio_data'])){
    $file = $_FILES['audio_data'];
    $filename = "Audio File Saved : " . "audio_news_" . time() . ".wav";
    $upload_path = "./audio_" . time() . ".wav";
    
    if(move_uploaded_file($file['tmp_name'], $upload_path)){
        file_put_contents("result.txt", $filename);
    }
}

?>

