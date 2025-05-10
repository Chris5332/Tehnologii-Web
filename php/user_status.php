<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

$is_admin=false;
if(isset($_SESSION['is_admin']))
    $is_admin=$_SESSION['is_admin'];

$is_family=false;
if(isset($_SESSION['is_family']))
    $is_family=$_SESSION['is_family'];

echo json_encode(["success" => true, "user_name" => $_SESSION['user_name'], "user_id" => $_SESSION['user_id'], "is_admin" => $is_admin, "is_family" => $is_family]);

?>