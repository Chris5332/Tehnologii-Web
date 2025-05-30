<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
    exit("You're NOT logged in.");

if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin']!=1)
    exit("You're NOT an admin.");

header('Content-Disposition: attachment; filename=animals_export.json');

$db = new SQLite3('../data/pow_db.sqlite');

$query=$db->prepare('SELECT id, name, species, breed, health_status, region, is_group, created_at FROM animals');
$result=$query->execute();

if(!$result)
    exit("Failed to access DataBase.");

$animals=[];

while($row=$result->fetchArray(SQLITE3_ASSOC))
{
    $animals[]=$row;
}

echo json_encode($animals, JSON_PRETTY_PRINT);

exit;
?>