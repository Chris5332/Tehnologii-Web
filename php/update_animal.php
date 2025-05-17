<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{ echo json_encode(["success" => false, "message" => "You're NOT logged in."]); exit;}

if(!isset($_POST['id'], $_POST['field'], $_POST['value']))
{ echo json_encode(["success" => false, "message" => "Fetch failed"]); exit;}

$userID=$_SESSION['user_id'];
$id=(int) $_POST['id'];
$field=$_POST['field'];
$value=trim($_POST['value']);

$db = new SQLite3('../data/pow_db.sqlite');

$errors=[];

if($field==="name")
{
    if($value==='')
        $errors[]="Name field can't be empty!";
    else if(!preg_match("/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/", $value))
        $errors[]="Each word in Name must start with a capital letter and contain only lower letters!";
    else if(strlen($value)<2)
        $errors[]="Name must have at least 2 letters!";
    else if(strlen($value)>15)
        $errors[]="Name must be less than 15 letters!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("UPDATE animals SET name=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update name."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Name updated successfully."]); exit;}
}

else if($field==="birth")
{
    if($value === '')
        $errors[]="Birth date can't be empty!";
    else if(strtotime($value)>time())
        $errors[]="Birth date cannot be from the future!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("UPDATE animals SET birth_date=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update birth date."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Birth date updated successfully."]); exit;}
}

else if($field==="species")
{
    if($value==='')
        $errors[]="Species field can't be empty!";
    else if(!preg_match("/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/", $value))
        $errors[]="Each word in Species must start with a capital letter and contain only lower letters!";
    else if(strlen($value)<2)
        $errors[]="Species must have at least 2 letters!";
    else if(strlen($value)>15)
        $errors[]="Species must be less than 15 letters!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("UPDATE animals SET species=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update species."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Species updated successfully."]); exit;}
}

else if($field==="breed")
{
    if($value==='')
        $errors[]="Breed field can't be empty!";
    else if(!preg_match("/^([A-Z][a-z]+)(\s[A-Z][a-z]+)*$/", $value))
        $errors[]="Each word in Breed must start with a capital letter and contain only lower letters!";
    else if(strlen($value)<2)
        $errors[]="Breed must have at least 2 letters!";
    else if(strlen($value)>25)
        $errors[]="Breed must be less than 25 letters!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("UPDATE animals SET breed=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update breed."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Breed updated successfully."]); exit;}
}

else if($field==="health_status")
{
    $valid_statuses=["healthy","injured","sick"];
    if(!in_array($value,$valid_statuses))
    { echo json_encode(["success" => false, "message" => "Invalid option selected for health."]); exit;}

    $query=$db->prepare("UPDATE animals SET health_status=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update health status."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Health status updated successfully."]); exit;}
}

else if($field==="region_status")
{
    $valid_statuses=["Botosani", "Bacau", "Galati", "Iasi", "Neamt", "Suceava"];
    if(!in_array($value,$valid_statuses))
    { echo json_encode(["success" => false, "message" => "Invalid option selected for region."]); exit;}

    $query=$db->prepare("UPDATE animals SET region=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update region."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Region status updated successfully."]); exit;}
}

else if($field==="pickup_address")
{
    if($value==='')
        $errors[]="Pickup address field can't be empty!";
    else if (!preg_match("/^[A-Z][a-zA-Z0-9\s.,-]*$/", $value))
        $errors[] = "Pickup address contains invalid characters!";
    else if(strlen($value)<6)
        $errors[]="Pickup address must have at least 6 letters!";
    else if(strlen($value)>40)
        $errors[]="Pickup address must be less than 40 letters!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("UPDATE animals SET pickup_address=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update pickup address."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Pickup address updated successfully."]); exit;}
}

else if($field==="description")
{
    if($value==='')
        $errors[]="Description field can't be empty!";
    else if (!preg_match("/^[A-Z][\p{L}\p{N} \t\n\r.,;:'\"!?()\-]*$/", $value))
        $errors[] = "Description contains invalid characters and must start with a capital letter!";
    else if(strlen($value)<6)
        $errors[]="Description must have at least 6 letters!";
    else if(strlen($value)>200)
        $errors[]="Description must be less than 200 letters!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("UPDATE animals SET description=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update description."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Description updated successfully."]); exit;}
}

else if($field==="feeding_schedule")
{
    if($value==='')
        $errors[]="Feeding schedule field can't be empty!";
    else if (!preg_match("/^[A-Z][\p{L}\p{N} \t\n\r.,;:'\"!?()\-]*$/", $value))
        $errors[] = "Feeding schedule contains invalid characters and must start with a capital letter!";
    else if(strlen($value)<6)
        $errors[]="Feeding schedule must have at least 6 letters!";
    else if(strlen($value)>150)
        $errors[]="Feeding schedule must be less than 150 letters!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("UPDATE animals SET feeding_schedule=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update feeding schedule."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Feeding schedule updated successfully."]); exit;}
}

else if($field==="restrictions")
{
    if($value==='')
        $errors[]="Restrictions field can't be empty!";
    else if (!preg_match("/^[A-Z][\p{L}\p{N} \t\n\r.,;:'\"!?()\-]*$/", $value))
        $errors[] = "Restrictions contains invalid characters and must start with a capital letter!";
    else if(strlen($value)<6)
        $errors[]="Restrictions must have at least 6 letters!";
    else if(strlen($value)>150)
        $errors[]="Restrictions must be less than 150 letters!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("UPDATE animals SET restrictions=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update restrictions."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Restrictions updated successfully."]); exit;}
}

else if($field==="relationship")
{
    if($value==='')
        $errors[]="Relationship field can't be empty!";
    else if (!preg_match("/^[A-Z][\p{L}\p{N} \t\n\r.,;:'\"!?()\-]*$/", $value))
        $errors[] = "Relationship contains invalid characters and must start with a capital letter!";
    else if(strlen($value)<6)
        $errors[]="Relationship must have at least 6 letters!";
    else if(strlen($value)>150)
        $errors[]="Relationship must be less than 150 letters!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("UPDATE animals SET relationship=:value WHERE id=:id AND owner_id=:user_id");
    $query->bindValue(":value",$value,SQLITE3_TEXT);
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to update relationship."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Relationship updated successfully."]); exit;}
}

else if($field==="images" && !isset($_FILES['images']))
    { echo json_encode(["success" => false, "message" => "Files not uploaded."]); exit; }
else if($field==="images" && isset($_FILES['images']))
{
    if(count($_FILES['images']['name'])>9)
    { echo json_encode(["success" => false, "message" => "You can only upload up to 9 images."]); exit; }

    $query=$db->prepare("SELECT path FROM media WHERE animal_id=:id AND type='photo'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    while($row = $result->fetchArray(SQLITE3_ASSOC))
    {
        $currentPath=$row['path'];
        $filePath=realpath(dirname(__FILE__) . '/../' . $currentPath);
        if($filePath && file_exists($filePath))
            unlink($filePath);
    }

    $query=$db->prepare("DELETE FROM media WHERE animal_id=:id AND type='photo'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    $valid_extensions=['jpg', 'jpeg', 'png'];

    for($i=0;$i<count($_FILES['images']['name']);$i++)
    {
        if (empty($_FILES['images']['name'][$i]))
            continue;
        if($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK)
        {
            $errors[]="Error uploading the file: " . $_FILES['images']['name'][$i];
            break;
        }

        $extension=strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
        if(!in_array($extension,$valid_extensions))
        {    
            $errors[]="Invalid extension for file: " . $_FILES['images']['name'][$i];
            break;
        }

        $fileName=$id . '_' . uniqid() . '.' . $extension;
        $path="../uploads/photos/$fileName";
        $pathForMedia="uploads/photos/$fileName";

        if(!move_uploaded_file($_FILES['images']['tmp_name'][$i], $path))
        {
            $errors[] = "Failed to save the file: " . $_FILES['images']['name'][$i];
            break;
        }
        $type='photo';
        $query=$db->prepare('INSERT INTO media(animal_id, type, path) VALUES(:id, :type, :path)');
        $query->bindValue(':id',$id,SQLITE3_INTEGER);
        $query->bindValue(':type',$type,SQLITE3_TEXT);
        $query->bindValue(':path',$pathForMedia,SQLITE3_TEXT);
        $result=$query->execute();
    }

    if(!empty($errors))
        { echo json_encode(["success" => false, "message" => $errors]); exit;}
    else
        { echo json_encode(["success" => true, "message" => "Images updated successfully."]); exit;}
}

else if($field==="videos" && !isset($_FILES['videos']))
    { echo json_encode(["success" => false, "message" => "Files not uploaded."]); exit; }
else if($field==="videos" && isset($_FILES['videos']))
{
    if(count($_FILES['videos']['name'])>3)
    { echo json_encode(["success" => false, "message" => "You can only upload up to 3 videos."]); exit; }

    $query=$db->prepare("SELECT path FROM media WHERE animal_id=:id AND type='video'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    while($row = $result->fetchArray(SQLITE3_ASSOC))
    {
        $currentPath=$row['path'];
        $filePath=realpath(dirname(__FILE__) . '/../' . $currentPath);
        if($filePath && file_exists($filePath))
            unlink($filePath);
    }

    $query=$db->prepare("DELETE FROM media WHERE animal_id=:id AND type='video'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    $valid_extensions=['mp4', 'webm'];

    for($i=0;$i<count($_FILES['videos']['name']);$i++)
    {
        if (empty($_FILES['videos']['name'][$i]))
            continue;
        if($_FILES['videos']['error'][$i] !== UPLOAD_ERR_OK)
        {
            $errors[]="Error uploading the file: " . $_FILES['videos']['name'][$i];
            break;
        }

        $extension=strtolower(pathinfo($_FILES['videos']['name'][$i], PATHINFO_EXTENSION));
        if(!in_array($extension,$valid_extensions))
        {    
            $errors[]="Invalid extension for file: " . $_FILES['videos']['name'][$i];
            break;
        }

        $fileName=$id . '_' . uniqid() . '.' . $extension;
        $path="../uploads/videos/$fileName";
        $pathForMedia="uploads/videos/$fileName";

        if(!move_uploaded_file($_FILES['videos']['tmp_name'][$i], $path))
        {
            $errors[] = "Failed to save the file: " . $_FILES['videos']['name'][$i];
            break;
        }
        $type='video';
        $query=$db->prepare('INSERT INTO media(animal_id, type, path) VALUES(:id, :type, :path)');
        $query->bindValue(':id',$id,SQLITE3_INTEGER);
        $query->bindValue(':type',$type,SQLITE3_TEXT);
        $query->bindValue(':path',$pathForMedia,SQLITE3_TEXT);
        $result=$query->execute();
    }

    if(!empty($errors))
        { echo json_encode(["success" => false, "message" => $errors]); exit;}
    else
        { echo json_encode(["success" => true, "message" => "Videos updated successfully."]); exit;}
}

else if($field==="audios" && !isset($_FILES['audios']))
    { echo json_encode(["success" => false, "message" => "Files not uploaded."]); exit; }
else if($field==="audios" && isset($_FILES['audios']))
{
    if(count($_FILES['audios']['name'])>3)
    { echo json_encode(["success" => false, "message" => "You can only upload up to 3 audios."]); exit; }

    $query=$db->prepare("SELECT path FROM media WHERE animal_id=:id AND type='audio'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    while($row = $result->fetchArray(SQLITE3_ASSOC))
    {
        $currentPath=$row['path'];
        $filePath=realpath(dirname(__FILE__) . '/../' . $currentPath);
        if($filePath && file_exists($filePath))
            unlink($filePath);
    }

    $query=$db->prepare("DELETE FROM media WHERE animal_id=:id AND type='audio'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    $valid_extensions=['mp3', 'wav'];

    for($i=0;$i<count($_FILES['audios']['name']);$i++)
    {
        if (empty($_FILES['audios']['name'][$i]))
            continue;
        if($_FILES['audios']['error'][$i] !== UPLOAD_ERR_OK)
        {
            $errors[]="Error uploading the file: " . $_FILES['audios']['name'][$i];
            break;
        }

        $extension=strtolower(pathinfo($_FILES['audios']['name'][$i], PATHINFO_EXTENSION));
        if(!in_array($extension,$valid_extensions))
        {    
            $errors[]="Invalid extension for file: " . $_FILES['audios']['name'][$i];
            break;
        }

        $fileName=$id . '_' . uniqid() . '.' . $extension;
        $path="../uploads/audios/$fileName";
        $pathForMedia="uploads/audios/$fileName";

        if(!move_uploaded_file($_FILES['audios']['tmp_name'][$i], $path))
        {
            $errors[] = "Failed to save the file: " . $_FILES['audios']['name'][$i];
            break;
        }
        $type='audio';
        $query=$db->prepare('INSERT INTO media(animal_id, type, path) VALUES(:id, :type, :path)');
        $query->bindValue(':id',$id,SQLITE3_INTEGER);
        $query->bindValue(':type',$type,SQLITE3_TEXT);
        $query->bindValue(':path',$pathForMedia,SQLITE3_TEXT);
        $result=$query->execute();
    }

    if(!empty($errors))
        { echo json_encode(["success" => false, "message" => $errors]); exit;}
    else
        { echo json_encode(["success" => true, "message" => "Audios updated successfully."]); exit;}
}

else if($field==="delete")
{
    $query=$db->prepare("SELECT path FROM media WHERE animal_id=:id");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

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

    $query=$db->prepare("DELETE FROM animals WHERE id=:id and owner_id=:user_id");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":user_id",$userID,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to delete pet."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Pet deleted successfully."]); exit;}
}

else if($field==="saveMedical")
{
    if(!isset($_POST['date']) || !isset($_POST['description']) || !isset($_POST['treatment']))
    { echo json_encode(["success" => false, "message" => "All fields are required for the medical history."]); exit;}

    $date=trim($_POST['date']);
    $description=trim($_POST['description']);
    $treatment=trim($_POST['treatment']);

    if($date === '')
        $errors[]="Date can't be empty!";
    else if(strtotime($date)>time())
        $errors[]="Date cannot be from the future!";

    else if($description==='')
        $errors[]="Description field can't be empty!";
    else if(!preg_match("/^[A-Z][\p{L}\p{N} \t\n\r.,;:'\"!?()\-]*$/", $description))
        $errors[]="Description contains invalid characters and must start with a capital letter!";
    else if(strlen($description)<6)
        $errors[]="Description must have at least 6 letters!";
    else if(strlen($description)>50)
        $errors[]="Description must be less than 50 letters!";

    else if($treatment==='')
        $errors[]="Treatment field can't be empty!";
    else if(!preg_match("/^[A-Z][\p{L}\p{N} \t\n\r.,;:'\"!?()\-]*$/", $treatment))
        $errors[]="Treatment contains invalid characters and must start with a capital letter!";
    else if(strlen($treatment)<6)
        $errors[]="Treatment must have at least 6 letters!";
    else if(strlen($treatment)>50)
        $errors[]="Treatment must be less than 50 letters!";

    if(!empty($errors))
    { echo json_encode(["success" => false, "message" => $errors]); exit; }

    $query=$db->prepare("INSERT INTO medical_history(animal_id, description, treatment, date) 
        VALUES(:id, :description, :treatment, :date)");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $query->bindValue(":description",$description,SQLITE3_TEXT);
    $query->bindValue(":treatment",$treatment,SQLITE3_TEXT);
    $query->bindValue(":date",$date,SQLITE3_TEXT);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to insert a new medical history."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Medical history updated successfully."]); exit;}
}

else if($field==="removeMedical")
{
    $query=$db->prepare("DELETE FROM medical_history WHERE animal_id=:id");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to delete medical history."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Medical history deleted successfully."]); exit;}
}

else if($field==="removeImages")
{
    $query=$db->prepare("SELECT path FROM media WHERE animal_id=:id AND type='photo'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    while($row = $result->fetchArray(SQLITE3_ASSOC))
    {
        $currentPath=$row['path'];
        $filePath=realpath(dirname(__FILE__) . '/../' . $currentPath);
        if($filePath && file_exists($filePath))
            unlink($filePath);
    }

    $query=$db->prepare("DELETE FROM media WHERE animal_id=:id AND type='photo'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to remove all images."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Images removed successfully."]); exit;}
}

else if($field==="removeVideos")
{
    $query=$db->prepare("SELECT path FROM media WHERE animal_id=:id AND type='video'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    while($row = $result->fetchArray(SQLITE3_ASSOC))
    {
        $currentPath=$row['path'];
        $filePath=realpath(dirname(__FILE__) . '/../' . $currentPath);
        if($filePath && file_exists($filePath))
            unlink($filePath);
    }

    $query=$db->prepare("DELETE FROM media WHERE animal_id=:id AND type='video'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to remove all videos."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Videos removed successfully."]); exit;}
}

else if($field==="removeAudios")
{
    $query=$db->prepare("SELECT path FROM media WHERE animal_id=:id AND type='audio'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    while($row = $result->fetchArray(SQLITE3_ASSOC))
    {
        $currentPath=$row['path'];
        $filePath=realpath(dirname(__FILE__) . '/../' . $currentPath);
        if($filePath && file_exists($filePath))
            unlink($filePath);
    }

    $query=$db->prepare("DELETE FROM media WHERE animal_id=:id AND type='audio'");
    $query->bindValue(":id",$id,SQLITE3_INTEGER);
    $result=$query->execute();

    if(!$result)
    { echo json_encode(["success" => false, "message" => "Failed to remove all audios."]); exit;}
    else
    { echo json_encode(["success" => true, "message" => "Audios removed successfully."]); exit;}
}

?>