<?php

session_start();

header("Content-Type: application/json");

if(!isset($_POST['name'])) { echo json_encode(["success" => false, "message" => "Missing field: name"]); exit;}
if(!isset($_POST['email'])) { echo json_encode(["success" => false, "message" => "Missing field: email"]); exit;}
if(!isset($_POST['confirm_email'])) { echo json_encode(["success" => false, "message" => "Missing field: confirm-email"]); exit;}
if(!isset($_POST['password'])) {echo json_encode(["success" => false, "message" => "Missing field: password"]); exit;}

$name=trim($_POST['name']);
if(!isset($_POST['surname']))
    $surname='';
else
    $surname=trim($_POST['surname']);
$email=trim($_POST['email']);
$confirm_email=trim($_POST['confirm_email']);
$password=$_POST['password'];
if(!isset($_POST['family']))
    $family=0;
else
    $family=1;

$db = new SQLite3('../data/pow_db.sqlite');

$errors=[];

$counterUpperCase=0;

for($i=0;$i<strlen($name);$i++)
    if($name[$i]>='A' && $name[$i]<='Z')
        $counterUpperCase++;

if($name==='')
    $errors[]="Name field can't be empty!";
else if(!preg_match("/^[A-Z]/",$name))
    $errors[]="Name must start with a capital letter!";
else if(strlen($name)<2)
    $errors[]="Name must have at least 2 letters!";
else if(strlen($name)>15)
    $errors[]="Name must be less than 15 letters!";
else if($counterUpperCase!==1)
    $errors[]="Name must contain a single upper case letter!";
else if(!preg_match("/^[A-Z][a-z]*$/",$name))
    $errors[]="Name can only contain letters!";

if($family===0)
{
    $counterUpperCase=0;

    for($i=0;$i<strlen($surname);$i++)
        if($surname[$i]>='A' && $surname[$i]<='Z')
            $counterUpperCase++;

    if($surname==='')
        $errors[]="Surname field can't be empty!";
    else if(!preg_match("/^[A-Z]/",$surname))
        $errors[]="Surname must start with a capital letter!";
    else if(strlen($surname)<2)
        $errors[]="Surname must have at least 2 letters!";
    else if(strlen($surname)>15)
        $errors[]="Surname must be less than 15 letters!";
    else if($counterUpperCase!==1)
        $errors[]="Surname must contain a single upper case letter!";
    else if(!preg_match("/^[A-Z][a-z]*$/",$surname))
        $errors[]="Surname can only contain letters!";
}

$counterUpperCase=0;

for($i=0;$i<strlen($email);$i++)
    if($email[$i]>='A' && $email[$i]<='Z')
        $counterUpperCase++;

$errorNumber=0;
if($email==='')
    {$errors[]="Email field can't be empty!"; $errorNumber=1;}
else if($counterUpperCase!==0)
    {$errors[]="Email must contain only lower case letters!"; $errorNumber=1;}
else if(strlen($email)>40)
    {$errors[]="Email must be less than 40 letters!"; $errorNumber=1;}
else if(!preg_match("/^[a-z][a-z0-9._]+@[a-z0-9.-]+\.[a-z]{2,}$/",$email))
    {$errors[]="Invalid email!"; $errorNumber=1;}

$query=$db->prepare("SELECT id FROM users WHERE email = :email");
$query->bindValue(':email', $email, SQLITE3_TEXT);
$result=$query->execute();
if($result->fetchArray())
{ $errors[]="Email already exists!"; $errorNumber=1;}    

if($confirm_email!==$email && $errorNumber===0)
    $errors[]="Emails must be the same!";

$digitsNumber=0;
$counterLowerCase=0;
$counterUpperCase=0;

for($i=0;$i<strlen($password);$i++)
    if($password[$i]>='A' && $password[$i]<='Z')
        $counterUpperCase++;
    else if($password[$i]>='0' && $password[$i]<='9')
        $digitsNumber++;
    else if($password[$i]>='a' && $password[$i]<='z')
        $counterLowerCase++;

if($password==="")
    $errors[]="Password field can't be empty!";
else if(strlen($password)<8)
    $errors[]="Password must have at least 8 characters!";
else if(strlen($password)>30)
    $errors[]="Password must be less than 30 characters!";
else if($counterUpperCase===0)
    $errors[]="Password must have at least one upper case letter!";
else if($digitsNumber===0)
    $errors[]="Password must have at least one digit!";
else if($counterLowerCase===0)
    $errors[]="Password must have at least one lower case letter!";

if(!empty($errors)) { echo json_encode(["success" => false, "errors" => $errors]); exit;}

$password= password_hash($password, PASSWORD_DEFAULT);

$query=$db->prepare('INSERT INTO users(name, surname, email, password, family) VALUES(:name, :surname, :email, :password, :family)');
$query->bindValue(':name', $name, SQLITE3_TEXT);
$query->bindValue(':surname', $surname, SQLITE3_TEXT);
$query->bindValue(':email', $email, SQLITE3_TEXT);
$query->bindValue(':password', $password, SQLITE3_TEXT);
$query->bindValue(':family', $family, SQLITE3_INTEGER);
$result=$query->execute();

if($result)
{
    $query=$db->prepare("SELECT id, name, admin FROM users WHERE email = :email");
    $query->bindValue(':email',$email,SQLITE3_TEXT);
    $result=$query->execute();
    $user=$result->fetchArray(SQLITE3_ASSOC);

    $_SESSION['user_id']=$user['id'];
    $_SESSION['user_name']=$user['name'];
    $_SESSION['is_admin']= ($user['admin']===1);

    echo json_encode(["success" => true, "message" => "Account created with no errors!", "user_id" => $user['id']]);
}
else
    echo json_encode(["success" => false, "message" => "Error at inserting into db!"]);

?>