<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

$is_admin=false;
if(isset($_SESSION['is_admin']))
    $is_admin=$_SESSION['is_admin'];

$is_family=false;
if(isset($_SESSION['is_family']))
    $is_family=$_SESSION['is_family'];

$user_id=$_SESSION['user_id'];

if(!isset($_POST['name'])) { echo json_encode(["success" => false, "message" => "Missing field: name"]); exit;}
if(!isset($_POST['birth'])) { echo json_encode(["success" => false, "message" => "Missing field: birth"]); exit;}
if(!isset($_POST['species'])) { echo json_encode(["success" => false, "message" => "Missing field: species"]); exit;}
if(!isset($_POST['breed'])) { echo json_encode(["success" => false, "message" => "Missing field: breed"]); exit;}
if(!isset($_POST['health'])) { echo json_encode(["success" => false, "message" => "Missing field: health"]); exit;}
if(!isset($_POST['pickup'])) { echo json_encode(["success" => false, "message" => "Missing field: pickup"]); exit;}

$name=trim($_POST['name']);
$birth=$_POST['birth'];
$species=trim($_POST['species']);
$breed=trim($_POST['breed']);
$health=$_POST['health'];
$pickup=trim($_POST['pickup']);
if(!isset($_POST['is_group']))
    $is_group=0; else $is_group=1;

$db = new SQLite3('../data/pow_db.sqlite');

$errors=[];

if($name==='')
    $errors[]="Name field can't be empty!";
else if(!preg_match("/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/", $name))
    $errors[]="Each word in Name must start with a capital letter and contain only lower letters!";
else if(strlen($name)<2)
    $errors[]="Name must have at least 2 letters!";
else if(strlen($name)>25)
    $errors[]="Name must be less than 25 letters!";

if($birth === '')
    $errors[]="Birth date can't be empty!";
else if(strtotime($birth)>time())
    $errors[]="Birth date cannot be from the future!";


if($species==='')
    $errors[]="Species field can't be empty!";
else if(!preg_match("/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/", $species))
    $errors[]="Each word in Species must start with a capital letter and contain only lower letters!";
else if(strlen($species)<2)
    $errors[]="Species must have at least 2 letters!";
else if(strlen($species)>25)
    $errors[]="Species must be less than 25 letters!";


if($breed==='')
    $errors[]="Breed field can't be empty!";
else if(!preg_match("/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/", $breed))
    $errors[]="Each word in Breed must start with a capital letter and contain only lower letters!";
else if(strlen($breed)<2)
    $errors[]="Breed must have at least 2 letters!";
else if(strlen($breed)>25)
    $errors[]="Breed must be less than 25 letters!";



$counterUpperCase=0;

for($i=0;$i<strlen($pickup);$i++)
    if($pickup[$i]>='A' && $pickup[$i]<='Z')
        $counterUpperCase++;

if($pickup==='')
    $errors[]="Pickup address field can't be empty!";
else if (!preg_match("/^[A-Z][a-zA-Z0-9\s.,-]*$/", $pickup))
    $errors[] = "Pickup address contains invalid characters!";
else if(strlen($pickup)<6)
    $errors[]="Pickup address must have at least 6 letters!";
else if(strlen($pickup)>40)
    $errors[]="Pickup address must be less than 40 letters!";


if(!empty($errors)) { echo json_encode(["success" => false, "errors" => $errors]); exit;}


$query=$db->prepare('INSERT INTO animals(name, birth_date, species, breed, health_status, pickup_address, is_group, owner_id) 
                    VALUES(:name, :birth, :species, :breed, :health, :pickup, :is_group, :user_id)');
$query->bindValue(':name',$name,SQLITE3_TEXT);
$query->bindValue(':birth',$birth,SQLITE3_TEXT);
$query->bindValue(':species',$species,SQLITE3_TEXT);
$query->bindValue(':breed',$breed,SQLITE3_TEXT);
$query->bindValue(':health',$health,SQLITE3_TEXT);
$query->bindValue(':pickup',$pickup,SQLITE3_TEXT);
$query->bindValue(':is_group',$is_group,SQLITE3_INTEGER);
$query->bindValue(':user_id',$user_id,SQLITE3_INTEGER);
$result=$query->execute();

if(!$result)
{ echo json_encode(["success" => false, "message" => "Failed to add a pet."]); exit;}


$animal_id=$db->lastInsertRowID();

$media_files=[
    'photos' => ['type' => 'photo' , 'max' => 9],
    'videos' => ['type' => 'video', 'max' => 3],
    'audios' => ['type' => 'audio', 'max' => 3]
];

$valid_extensions=[
    'photo' => ['jpg', 'jpeg', 'png'],
    'video' => ['mp4', 'webm'],
    'audio' => ['mp3', 'wav']
];

$stopValue=0;

foreach($media_files as $key => $field)
{
    if($stopValue===1)
        break;

    if(!isset($_FILES[$key]))
        continue;

    $type=$field['type'];
    $maxi=$field['max'];
    $files=$_FILES[$key];

    $numberOfFiles=count($files['name']);

    if($numberOfFiles>$maxi)
    {
        $errors[]="Maximum number of uploads exceeded!";
        $stopValue=1;
        break;
    }
    
    
    for($i=0; $i<$numberOfFiles; $i++)
    {
        if (empty($files['name'][$i]))
            continue;
        
        if($files['error'][$i] !== UPLOAD_ERR_OK)
        {
            $errors[]="Error uploading the file: " . $files['name'][$i];
            $stopValue=1;
            break;
        }

        
        
        $extension=strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        if(!in_array($extension,$valid_extensions[$type]))
        {    
            $errors[]="Invalid extension for file: " . $files['name'][$i];
            $stopValue=1;
            break;
        }

        $fileName=$animal_id . '_' . uniqid() . '.' . $extension;
        $path="../uploads/{$type}s/$fileName";

        if(!move_uploaded_file($files['tmp_name'][$i], $path))
        {
            $errors[] = "Failed to save the file: " . $files['name'][$i];
            $stopValue=1;
            break;
        }

        $query=$db->prepare('INSERT INTO media(animal_id, type, path) VALUES(:animal_id, :type, :path)');
        $query->bindValue(':animal_id',$animal_id,SQLITE3_INTEGER);
        $query->bindValue(':type',$type,SQLITE3_TEXT);
        $query->bindValue(':path',$path,SQLITE3_TEXT);

        $result=$query->execute();

        if(!$result)
        {
            $errors[] = "Failed to insert into media";
            $stopValue=1;
            break;
        }

    }
}

if(!empty($errors))
{
    $response=$db->query("SELECT path FROM media WHERE animal_id=$animal_id");
    
    while($row = $response->fetchArray(SQLITE3_ASSOC))
    {
        $currentPath=$row['path'];
        $filePath=realpath(dirname(__FILE__) . '/../' . $currentPath);
        if($filePath && file_exists($filePath))
            unlink($filePath);
    }

    $query=$db->prepare("DELETE FROM media WHERE animal_id = :id");
    $query->bindValue(':id',$animal_id,SQLITE3_INTEGER);
    $query->execute();

    $query=$db->prepare("DELETE FROM animals WHERE id=:id");
    $query->bindValue(':id',$animal_id,SQLITE3_INTEGER);
    $query->execute();

    echo json_encode(["success" => false, "errors" => $errors]);
    exit;
}


echo json_encode(["success" => true, "message" => "Pet added with success.", "user_name" => $_SESSION['user_name'], "user_id" => $user_id, "is_admin" => $is_admin, "is_family" => $is_family]);

?>