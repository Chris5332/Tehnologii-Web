<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

$db = new SQLite3('../data/pow_db.sqlite');
$query=$db->prepare('SELECT a.*,(SELECT path FROM media m WHERE m.animal_id=a.id AND m.type="photo" LIMIT 1) 
AS image_path FROM animals a WHERE a.owner_id=:userID');
$query->bindValue(':userID',$_SESSION['user_id'],SQLITE3_TEXT);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to get all the pets from DataBase."]); exit;}

$petList=[];

while($row=$result->fetchArray(SQLITE3_ASSOC))
    $petList[]=$row;

echo json_encode(["success" => true, "message" => "All pets obtained.", "data" => $petList])
?>