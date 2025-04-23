<?php

require_once "../scripts/protect.php";

$db = new SQLite3('../data/pow_db.sqlite');

$db->exec('CREATE TABLE IF NOT EXISTS "users"(
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "name" TEXT NOT NULL,
    "surname" TEXT,
    "email" TEXT NOT NULL UNIQUE,
    "password" TEXT NOT NULL,
    "family" INTEGER DEFAULT 0,
    "admin" INTEGER DEFAULT 0,
    "created_at" TIME DEFAULT CURRENT_TIMESTAMP
)');

echo "bd-created";
?>