<?php

session_start();

header("Content-Type: application/json");

$errors=[];

if(!isset($_POST['email'])) { echo json_encode(["success" => false, "message" => "Missing field: email"]); exit;}
if(!isset($_POST['password'])) { echo json_encode(["success" => false, "message" => "Missing field: password"]); exit;}


$email=trim($_POST['email']);
$password=$_POST['password'];

if($email==='')
    $errors[]="Email field can't be empty!";
if($password==='')
    $errors[]="Password field can't be empty!";

$db=new SQLite3('../data/pow_db.sqlite');

$query=$db->prepare("SELECT id, password, name, admin, family FROM users WHERE email = :email");
$query->bindValue(':email',$email,SQLITE3_TEXT);
$result=$query->execute();
$user=$result->fetchArray(SQLITE3_ASSOC);

if(!$user)
    $errors[]="Can't find email in database!";
else if(!password_verify($password,$user['password']))
        $errors[]="Invalid password for that email!";

if(!empty($errors)) { echo json_encode(["success" => false, "errors" => $errors]); exit;}

$_SESSION['user_id']=$user['id'];
$_SESSION['user_name']=$user['name'];
$_SESSION['is_admin']=($user['admin']===1);
$_SESSION['is_family']=($user['family']===1);

echo json_encode(["success" => true, "message" => "Login successful!", "user_id" => $user['id']]);

?>