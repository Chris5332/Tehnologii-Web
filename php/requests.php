<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

$db = new SQLite3('../data/pow_db.sqlite');
$option="received";
if(isset($_GET['filter']) && is_string($_GET['filter']))
    $option=strtolower(trim($_GET['filter']));

$validEntries=["received","sent"];

if(!in_array($option,$validEntries))
    $option="received";


if($option==="received")
{
    $query=$db->prepare('SELECT r.id AS request_id, r.user_id, u.name AS user_name,u.family AS is_family, 
    a.id AS animal_id,a.name AS animal_name, a.is_group FROM requests r JOIN users u ON r.user_id=u.id 
    JOIN animals a ON r.animal_id=a.id WHERE a.owner_id=:userID');
    $query->bindValue(":userID",$_SESSION['user_id'],SQLITE3_INTEGER);
    $result=$query->execute();
    
    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to get all the requests from DataBase."]); exit;}

    $data=[];
    while($row=$result->fetchArray(SQLITE3_ASSOC))
        $data[]=$row;

    echo json_encode(["success" => true, "message" => "All requests obtained.", "data" => $data]);
    exit;
}
else if($option==="sent")
{
    $query=$db->prepare('SELECT animal_name, animal_id, is_group, message, id AS notification_id FROM notifications 
    WHERE user_id =:userID ORDER BY created_at DESC');
    $query->bindValue(":userID",$_SESSION['user_id'],SQLITE3_INTEGER);
    $result=$query->execute();
    
    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to get all the requests from DataBase."]); exit;}

    $data=[];
    while($row=$result->fetchArray(SQLITE3_ASSOC))
        $data[]=$row;

    echo json_encode(["success" => true, "message" => "All requests obtained.", "data" => $data]);
    exit;
}
else
{
    echo json_encode(["success" => false, "message" => "Failed to get your requests."]);
    exit;
}
?>