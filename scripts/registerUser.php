<?php

//require("../db/Conn.php");
require("../db/MySQLDAO.php");
$config = parse_ini_file("../../../SwiftCourse2.ini");


$returnValue = array();

if(empty($_REQUEST["userEmail"]) || empty($_REQUEST["userPassword"])
        || empty($_REQUEST["userFirstName"])
        || empty($_REQUEST["userLastName"]))
    {
        $returnValue["status"]="400";
        $returnValue["message"]= "Missing required information";
        echo json_encode($returnValue);
        return;
    }
//prevent SQL query injection
$userEmail = htmlentities($_REQUEST["userEmail"]);
$userPassword = htmlentities($_REQUEST["userPassword"]);
$userFirstName = htmlentities($_REQUEST["userFirstName"]);
$userLastName = htmlentities($_REQUEST["userLastName"]);

//sequre password before storing to database
$salt = openssl_random_pseudo_bytes(16);
$secured_password = sha1($userPassword . $salt);

//trim array from ini 
$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
$dbpass = trim($config["dbpass"]);
$dbname = trim($config["dbname"]);

$dao = new MySQLDAO($dbhost, $dbuser, $dbpass, $dbname);
$dao->openConnection();

//check is user already in the database
$userDetails = $dao->getUserDetails($userEmail);
if(!empty($userDetails))
    {
        $returnValue["status"]="400";
        $returnValue["message"]= "Please choose a different email address";
        echo json_encode($returnValue);
        return;
    }
//register new user

$result =$dao->registerUser($userEmail, $userFirstName, $userLastName, $secured_password, $salt);

if($result){
    $userDetails = $dao->getUserDetails($userEmail);
    $returnValue["status"]="200";
    $returnValue["message"]= "Successfully registred user";
    $returnValue["userId"] = $userDetails["user_id"];
    $returnValue["userFirstName"] = $userDetails["first_name"];
    $returnValue["userLastName"] = $userDetails["last_name"];
    $returnValue["userEmail"] = $userDetails["email"];
    
} else {
    $returnValue["status"]="400";
    $returnValue["message"]= "Could not register user with provided information";
}

$dao->closeConnection();

echo json_encode($returnValue);