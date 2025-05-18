<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

if(!isset($_GET['id']))
{ echo json_encode(["success" => false, "message" => "Pet not found"]); exit;}

$userID=$_SESSION['user_id'];
$id=(int) $_GET['id'];

$db = new SQLite3('../data/pow_db.sqlite');
$query=$db->prepare("SELECT * FROM animals WHERE id=:id AND owner_id!=:user_id");
$query->bindValue(":id", $id, SQLITE3_INTEGER);
$query->bindValue(":user_id", $userID, SQLITE3_INTEGER);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to get pet info from DataBase."]); exit;}

$pet=$result->fetchArray(SQLITE3_ASSOC);

if(!$pet)
{ echo json_encode(["success" => false, "message" => "Pet not found or you don't have access."]); exit;}

$query=$db->prepare("SELECT type, path FROM media WHERE animal_id=:id");
$query->bindValue(":id", $id, SQLITE3_INTEGER);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to get pet media from DataBase."]); exit;}

$mediaList=[];
while($row=$result->fetchArray(SQLITE3_ASSOC))
    $mediaList[]=$row;

$query=$db->prepare("SELECT description, treatment, date FROM medical_history WHERE animal_id=:id ORDER BY date DESC");
$query->bindValue(":id", $id, SQLITE3_INTEGER);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to get pet medical history from DataBase."]); exit;}

$medicalList=[];
while($row=$result->fetchArray(SQLITE3_ASSOC))
    $medicalList[]=$row;

echo json_encode(["success" => true, "message" => "All info obtained.", "data" => $pet, "data_media" => $mediaList, "data_medical" => $medicalList]);
?>