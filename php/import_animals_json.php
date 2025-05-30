<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

$userID=$_SESSION['user_id'];

if(!isset($_FILES['json_file']) || $_FILES['json_file']['error']!== UPLOAD_ERR_OK)
{ echo json_encode(["success" => false, "message" => "Uploading json file failded!"]); exit;}

$extension=strtolower(pathinfo($_FILES['json_file']['name'], PATHINFO_EXTENSION));
if($extension!=='json')
{ echo json_encode(["success" => false, "message" => "Only JSON file types are allowed!"]); exit;}

$jsonFile=$_FILES['json_file']['tmp_name'];
$jsonContent=file_get_contents($jsonFile);
$array=json_decode($jsonContent,true);

if(!is_array($array))
{ echo json_encode(["success" => false, "message" => "Invalid JSON structure!"]); exit;}

$db = new SQLite3('../data/pow_db.sqlite');

$imported=0;
$non_imported=0;

foreach($array as $data)
{
    $validFields=['name','birth_date','species','breed','health_status','pickup_address','region','is_group'];
    if(!is_array($data) || array_diff($validFields,array_keys($data)))
    {
        $non_imported++;
        continue;
    }

    $errors=0;

    $name=trim($data['name']);
    $birth_date=trim($data['birth_date']);
    $species=trim($data['species']);
    $breed=trim($data['breed']);
    $health_status=trim($data['health_status']);
    $pickup_address=trim($data['pickup_address']);
    $region=trim($data['region']);
    $is_group=trim($data['is_group']);

    $query=$db->prepare('INSERT INTO animals(name, birth_date, species, breed, health_status, region, pickup_address, is_group, owner_id) 
        VALUES(:name,:birth_date,:species,:breed,:health_status,:region,:pickup_address,:is_group,:owner_id)');

    if($name==='')
        $errors=1;
    else if(!preg_match("/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/", $name))
        $errors=1;
    else if(strlen($name)<2)
        $errors=1;
    else if(strlen($name)>15)
        $errors=1;

    if($birth_date === '')
        $errors=1;
    else if(strtotime($birth_date)>time())
        $errors=1;

    if($species==='')
        $errors=1;
    else if(!preg_match("/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/", $species))
        $errors=1;
    else if(strlen($species)<2)
        $errors=1;
    else if(strlen($species)>15)
        $errors=1;

    if($breed==='')
        $errors=1;
    else if(!preg_match("/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/", $breed))
        $errors=1;
    else if(strlen($breed)<2)
        $errors=1;
    else if(strlen($breed)>25)
        $errors=1;

    if(!in_array($health_status,['healthy','injured','sick']))
        $errors=1;

    if($pickup_address==='')
        $errors=1;
    else if (!preg_match("/^[A-Z][a-zA-Z0-9\s.,-]*$/", $pickup_address))
        $errors=1;
    else if(strlen($pickup_address)<6)
        $errors=1;
    else if(strlen($pickup_address)>40)
        $errors=1;

    if(!in_array($region,['Botosani','Bacau','Galati','Iasi','Neamt','Suceava']))
        $errors=1;

    if(!in_array((int)$is_group,[0,1]))
        $errors=1;

    if($errors===0)
    {
        $query->bindValue(':name', $name, SQLITE3_TEXT);
        $query->bindValue(':birth_date', $birth_date, SQLITE3_TEXT);
        $query->bindValue(':species', $species, SQLITE3_TEXT);
        $query->bindValue(':breed', $breed, SQLITE3_TEXT);
        $query->bindValue(':health_status', $health_status, SQLITE3_TEXT);
        $query->bindValue(':region', $region, SQLITE3_TEXT);
        $query->bindValue(':pickup_address', $pickup_address, SQLITE3_TEXT);
        $query->bindValue(':is_group', (int)$is_group, SQLITE3_INTEGER);
        $query->bindValue(':owner_id', $userID, SQLITE3_INTEGER);
        $result=$query->execute();
        if($result)
            $imported+=1;
    }
    else
        $non_imported+=1;

}

echo json_encode(["success" => true, "message" => "Successfully imported $imported animals via JSON file!\nIgnored $non_imported animals!"]);
?>