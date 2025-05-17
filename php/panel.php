<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin']!=1)
{ echo json_encode(["success" => false, "message" => "You're NOT an admin."]); exit;}

if(!isset($_POST['deletePet']) || trim($_POST['deletePet'])==="")
{ echo json_encode(["success" => false, "message" => "No pet id provided."]); exit;}

$id=trim($_POST['deletePet']);

if(!preg_match('/^\d+$/',$id))
{ echo json_encode(["success" => false, "message" => "Invalid characters for ID."]); exit;}

$db = new SQLite3('../data/pow_db.sqlite');

$query=$db->prepare("SELECT COUNT(*) as counter FROM animals WHERE id=:id");
$query->bindValue(":id",$id,SQLITE3_INTEGER);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to access DataBase."]); exit;}

$counter=$result->fetchArray(SQLITE3_ASSOC)['counter'];

if($counter===0)
{ echo json_encode(["success" => false, "message" => "Pet cannot be found."]); exit;}

$query=$db->prepare("SELECT path FROM media WHERE animal_id=:id");
$query->bindValue(":id",$id,SQLITE3_INTEGER);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Delete failed."]); exit;}

while($row = $result->fetchArray(SQLITE3_ASSOC))
{
    $currentPath=$row['path'];
    $filePath=realpath(dirname(__FILE__) . '/../' . $currentPath);
    if($filePath && file_exists($filePath))
        unlink($filePath);
}

$query=$db->prepare("DELETE FROM media WHERE animal_id=:id");
$query->bindValue(":id",$id,SQLITE3_INTEGER);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to delete media."]); exit;}
    
$query=$db->prepare("DELETE FROM medical_history WHERE animal_id=:id");
$query->bindValue(":id",$id,SQLITE3_INTEGER);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to delete medical history."]); exit;}

$query=$db->prepare("DELETE FROM animals WHERE id=:id");
$query->bindValue(":id",$id,SQLITE3_INTEGER);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to delete pet."]); exit;}
else
{ echo json_encode(["success" => true, "message" => "Pet deleted successfully."]); exit;}
?>