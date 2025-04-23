<?php

session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']))
{
    echo '<script>location.hash="notfound"; </script>';
    exit;
}

if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin']!==1)
{
    echo '<script>location.hash="notfound"; </script>';
    exit;
}

?>