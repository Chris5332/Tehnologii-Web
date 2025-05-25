<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

$db = new SQLite3('../data/pow_db.sqlite');
$option="all";
if(isset($_GET['filter']) && is_string($_GET['filter']))
    $option=trim($_GET['filter']);

$validEntries=["all","group","single","mostrecent","mostpopular","useracc","familyacc","healthy","injured","sick",
"botosani","bacau","galati","iasi","neamt","suceava"];

$isSpecies=0;

if(!in_array($option,$validEntries))
{
    $querySpecies=$db->prepare("SELECT * FROM animals WHERE species=:species LIMIT 1");
    $querySpecies->bindValue(":species",$option,SQLITE3_TEXT);
    $resultSpecies=$querySpecies->execute();

    if($resultSpecies && $resultSpecies->fetchArray())
        $isSpecies=1;
}

if(!in_array($option,$validEntries) && $isSpecies===0)
    $option="all";

$queryText='SELECT a.*,(SELECT path FROM media m WHERE m.animal_id=a.id AND m.type="photo" LIMIT 1) 
    AS image_path FROM animals a WHERE a.owner_id!=:userID';

$type_bind=0;

if($option==="group")
    $queryText .= ' AND a.is_group=1';
else if($option==="single")
    $queryText .= ' AND a.is_group=0';
else if($option==="mostrecent")
    $queryText .= ' ORDER BY a.created_at DESC';
else if(in_array($option,["healthy","injured","sick"]))
{
    $queryText .= ' AND a.health_status=:type';
    $type_bind=1;
    $type=$option;
}
else if(in_array($option,["botosani","bacau","galati","iasi","neamt","suceava"]))
{
    $queryText .= ' AND a.region=:type';
    $type_bind=1;
    $type=ucfirst($option);
}
else if($option==="mostpopular")
{
    $queryText='SELECT a.*,(SELECT path FROM media m WHERE m.animal_id=a.id AND m.type="photo" LIMIT 1) 
        AS image_path,(SELECT COUNT(*) FROM requests r WHERE r.animal_id=a.id) AS request_count FROM animals a 
            WHERE a.owner_id!=:userID ORDER BY request_count DESC';
}
else if($option==="familyacc")
{
    $queryText='SELECT a.*,(SELECT path FROM media m WHERE m.animal_id=a.id AND m.type="photo" LIMIT 1) 
        AS image_path FROM animals a JOIN users u ON a.owner_id=u.id WHERE a.owner_id!=:userID AND u.family=1';
}
else if($option==="useracc")
{
    $queryText='SELECT a.*,(SELECT path FROM media m WHERE m.animal_id=a.id AND m.type="photo" LIMIT 1) 
        AS image_path FROM animals a JOIN users u ON a.owner_id=u.id WHERE a.owner_id!=:userID AND u.family=0';
}
else if(!in_array($option,$validEntries))
{
    $queryText .= ' AND a.species=:type';
    $type_bind=1;
    $type=$option;
}

$query=$db->prepare($queryText);
$query->bindValue(':userID',$_SESSION['user_id'],SQLITE3_INTEGER);
if($type_bind===1)
    $query->bindValue(':type',$type,SQLITE3_TEXT);

$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to get all the pets from DataBase."]); exit;}

$petList=[];

while($row=$result->fetchArray(SQLITE3_ASSOC))
    $petList[]=$row;

echo json_encode(["success" => true, "message" => "All pets obtained.", "data" => $petList])
?>