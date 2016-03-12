<?php

//require("../db/Conn.php");
require("../db/MySQLDAO.php");
$config = parse_ini_file("../../../SwiftCourse.ini");

$returnValue = array();
if(empty($_REQUEST["userEmail"]) || empty($_REQUEST["userPassword"]))
    {
        $returnValue["status"]="400";
        $returnValue["message"]= "Missing required information";
        echo json_encode($returnValue);
        return;
    }

$userEmail = htmlentities($_REQUEST["userEmail"]);
$userPassword = htmlentities($_REQUEST["userPassword"]);
    
    //trim array from ini 
$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
//should have similar name like in ini file
$dbpass = trim($config["dbpass"]);
$dbname = trim($config["dbname"]);

$dao = new MySQLDAO($dbhost, $dbuser, $dbpass, $dbname);
$dao->openConnection();

//check is user already in the database
$userDetails = $dao->getUserDetails($userEmail);

if(empty($userDetails))
    {
        $returnValue["status"]="401";
        $returnValue["message"]= "User not found";
        echo json_encode($returnValue);
        return;
    }
    
$userSecuredPassword = $userDetails["user_password"];
$userSalt = $userDetails["salt"];

// check if user sended password equal to the encrypted password from the 
// database
if($userSecuredPassword === sha1($userPassword . $userSalt)){
    
    $returnValue["status"]="200";
    $returnValue["userFirstName"] = $userDetails["first_name"];
    $returnValue["userLastName"] = $userDetails["last_name"];
    $returnValue["userEmail"] = $userDetails["email"];
    $returnValue["userId"] = $userDetails["user_id"];
    
} else {
    
    $returnValue["status"]="403";
    $returnValue["message"]= "User not found";
    echo json_encode($returnValue);
    return;
}

$dao->closeConnection();

echo json_encode($returnValue);