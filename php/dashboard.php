<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

echo json_encode(["success" => true, "user_name" => $_SESSION['user_name'], "user_id" => $_SESSION['user_id']]);

?>