<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

$db = new SQLite3('../data/pow_db.sqlite');

$query=$db->prepare('SELECT DISTINCT species FROM animals ORDER BY species ASC');
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to get all the species from DataBase."]); exit;}

$speciesList=[];

while($row=$result->fetchArray(SQLITE3_ASSOC))
    $speciesList[]=$row['species'];

echo json_encode(["success" => true, "message" => "All species obtained.", "data" => $speciesList])
?>