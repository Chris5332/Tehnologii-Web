<?php

session_start();

header("Content-Type: application/json");

if(!isset($_POST['email'])) { echo json_encode(["success" => false, "message" => "Missing field: email"]); exit;}
if(!isset($_POST['password'])) { echo json_encode(["success" => false, "message" => "Missing field: password"]); exit;}


$email=trim($_POST['email']);
$password=$_POST['password'];

$db=new SQLite3('../data/pow_db.sqlite');

$query=$db->prepare("SELECT id, password, name, admin FROM users WHERE email = :email");
$query->bindValue(':email',$email,SQLITE3_TEXT);
$result=$query->execute();
$user=$result->fetchArray(SQLITE3_ASSOC);

if(!$user) { echo json_encode(["success" => false, "message" => "Invalid input."]); exit;}

if(!password_verify($password,$user['password']))
{ echo json_encode(["success" => false, "message" => "Invalid input."]); exit;}

$_SESSION['user_id']=$user['id'];
$_SESSION['user_name']=$user['name'];
$_SESSION['is_admin']=($user['admin']===1);

echo json_encode(["success" => true, "message" => "Login successful!", "user_id" => $user['id']]);

?>