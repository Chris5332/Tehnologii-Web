<?php

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
    exit("You're NOT logged in.");

if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin']!=1)
    exit("You're NOT an admin.");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=animals_export.csv');

$db = new SQLite3('../data/pow_db.sqlite');

$query=$db->prepare('SELECT id, name, species, breed, health_status, region, is_group, created_at FROM animals');
$result=$query->execute();

if(!$result)
    exit("Failed to access DataBase.");

$output = fopen( 'php://output', 'w' );
$header_args = array( 'ID', 'Name', 'Species', 'Breed', 'Health Status', 'Region', 'Is Group', 'Created At');
fputcsv( $output, $header_args );

while($row=$result->fetchArray(SQLITE3_ASSOC))
{
    fputcsv($output,$row);
}

fclose($output);
exit;
?>