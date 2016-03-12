<?php

$user_id = $_POST["userId"];
$target_dir = "/Applications/MAMP/htdocs/SwiftAppAndMySQL/profile-pictures/" . $user_id;

if(!file_exists($target_dir)){
    
    mkdir($target_dir, 0777, true);
}

//get trailing file name
$target_dir = $target_dir . "/" . basename($_FILES["files"]["name"]);

if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir)){
    echo json_encode([
        "Message" => "The file ". basename($_FILES["file"]["name"]). " has been uploaded",
        "Status" => "OK", "userId" => $user_id
    ]);
} else {
    echo json_encode([
        "Message" => "Sorry, there was an error uploading file.",
        "Status" => "Error", "userId" => $user_id
        
    ]);
}