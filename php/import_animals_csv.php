<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

$userID=$_SESSION['user_id'];

if(!isset($_FILES['csv_file']) || $_FILES['csv_file']['error']!== UPLOAD_ERR_OK)
{ echo json_encode(["success" => false, "message" => "Uploading csv file failded!"]); exit;}

$extension=strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
if($extension!=='csv')
{ echo json_encode(["success" => false, "message" => "Only CSV file types are allowed!"]); exit;}

$csvfile=$_FILES['csv_file']['tmp_name'];
$db = new SQLite3('../data/pow_db.sqlite');

$file = fopen($csvfile, "r");
if(!$file)
{ echo json_encode(["success" => false, "message" => "Failed to open the CSV file!"]); exit;}

$imported=0;
$non_imported=0;

while(($data = fgetcsv($file, 10000, ",")) !== FALSE)
{
    if(!is_array($data) || count($data)!==8)
    {
        $non_imported++;
        continue;
    }
        

    $errors=0;

    $name=trim($data[0]);
    $birth_date=trim($data[1]);
    $species=trim($data[2]);
    $breed=trim($data[3]);
    $health_status=trim($data[4]);
    $pickup_address=trim($data[5]);
    $region=trim($data[6]);
    $is_group=trim($data[7]);

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

fclose($file);
echo json_encode(["success" => true, "message" => "Successfully imported $imported animals via CSV file!\nIgnored $non_imported animals!"]);
?>