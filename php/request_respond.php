<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

if(!isset($_POST['id'], $_POST['field']))
{ echo json_encode(["success" => false, "message" => "Fetch failed"]); exit;}

$userID=$_SESSION['user_id'];
$id=(int) $_POST['id'];
$field=$_POST['field'];

$db = new SQLite3('../data/pow_db.sqlite');

if($field==="accept")
{
    $query=$db->prepare("SELECT r.user_id, a.id AS animal_id, a.name AS animal_name, a.is_group FROM 
        requests r JOIN animals a ON a.id=r.animal_id WHERE r.id=:req_id AND a.owner_id=:userID");
    $query->bindValue(":req_id",$id,SQLITE3_INTEGER);
    $query->bindValue(":userID",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to get data about requests."]); exit;}

    $row=$result->fetchArray(SQLITE3_ASSOC);
    
    $animal_id=$row['animal_id'];
    $animal_name=$row['animal_name'];
    $animal_group=$row['is_group'];
    $adoption_user=$row['user_id'];
    $message="accepted";

    $query=$db->prepare("INSERT INTO notifications(user_id,message,animal_id,animal_name,is_group) VALUES 
        (:userID,:message,:animal_id,:animal_name,:is_group)");
    $query->bindValue(":userID",$adoption_user,SQLITE3_INTEGER);
    $query->bindValue(":message",$message,SQLITE3_TEXT);
    $query->bindValue(":animal_id",$animal_id,SQLITE3_INTEGER);
    $query->bindValue(":animal_name",$animal_name,SQLITE3_TEXT);
    $query->bindValue(":is_group",$animal_group,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to insert into notifications."]); exit;}

    $query=$db->prepare("DELETE FROM requests WHERE animal_id=:animal_id");
    $query->bindValue(":animal_id",$animal_id,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to delete all other requests for that pet."]); exit;}

    $query=$db->prepare("SELECT path FROM media WHERE animal_id=:animal_id");
    $query->bindValue(":animal_id",$animal_id,SQLITE3_INTEGER);
    $result=$query->execute();

    while($row = $result->fetchArray(SQLITE3_ASSOC))
    {
        $currentPath=$row['path'];
        $filePath=realpath(dirname(__FILE__) . '/../' . $currentPath);
        if($filePath && file_exists($filePath))
            unlink($filePath);
    }

    $query=$db->prepare("DELETE FROM media WHERE animal_id=:animal_id");
    $query->bindValue(":animal_id",$animal_id,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to delete media."]); exit;}
    
    $query=$db->prepare("DELETE FROM medical_history WHERE animal_id=:animal_id");
    $query->bindValue(":animal_id",$animal_id,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to delete medical history."]); exit;}

    $query=$db->prepare("DELETE FROM animals WHERE id=:animal_id and owner_id=:user_id");
    $query->bindValue(":animal_id",$animal_id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to delete pet."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Request accepted. Pet was deleted from our database."]); exit;}
}
else if($field==="decline")
{
    $query=$db->prepare("SELECT r.user_id, a.id AS animal_id, a.name AS animal_name, a.is_group FROM 
        requests r JOIN animals a ON a.id=r.animal_id WHERE r.id=:req_id AND a.owner_id=:userID");
    $query->bindValue(":req_id",$id,SQLITE3_INTEGER);
    $query->bindValue(":userID",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to get data about requests."]); exit;}

    $row=$result->fetchArray(SQLITE3_ASSOC);
    
    $animal_id=$row['animal_id'];
    $animal_name=$row['animal_name'];
    $animal_group=$row['is_group'];
    $adoption_user=$row['user_id'];
    $message="declined";

    $query=$db->prepare("INSERT INTO notifications(user_id,message,animal_id,animal_name,is_group) VALUES 
        (:userID,:message,:animal_id,:animal_name,:is_group)");
    $query->bindValue(":userID",$adoption_user,SQLITE3_INTEGER);
    $query->bindValue(":message",$message,SQLITE3_TEXT);
    $query->bindValue(":animal_id",$animal_id,SQLITE3_INTEGER);
    $query->bindValue(":animal_name",$animal_name,SQLITE3_TEXT);
    $query->bindValue(":is_group",$animal_group,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to insert into notifications."]); exit;}

    $query=$db->prepare("DELETE FROM requests WHERE id=:id");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to delete request."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Request declined."]); exit;}
}
else if($field==="removeNotification")
{
    $query=$db->prepare("DELETE FROM notifications WHERE id=:notification_id AND user_id=:userID");
    $query->bindValue(":notification_id",$id,SQLITE3_INTEGER);
    $query->bindValue(":userID",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to delete notification."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Notification cleared."]); exit;}
}

?>