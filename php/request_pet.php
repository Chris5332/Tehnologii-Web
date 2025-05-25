<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

if(!isset($_POST['id'], $_POST['field'], $_POST['value']))
{ echo json_encode(["success" => false, "message" => "Fetch failed"]); exit;}

$userID=$_SESSION['user_id'];
$id=(int) $_POST['id'];
$field=$_POST['field'];
$value=trim($_POST['value']);

$db = new SQLite3('../data/pow_db.sqlite');

$errors=[];

if($field==="request")
{
    $query=$db->prepare("SELECT COUNT(*) AS count FROM requests WHERE animal_id=:id AND user_id=:user_id");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to count requests."]); exit;}

    $row=$result->fetchArray(SQLITE3_ASSOC);
    if($row && $row['count']>0)
    { echo json_encode(["success" => false, "message" => "You have already requested the pet."]); exit;}
    
    $query=$db->prepare("INSERT INTO requests(animal_id, user_id) VALUES(:id, :user_id)");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to request pet."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Request sent successfully."]); exit;}
}

?>