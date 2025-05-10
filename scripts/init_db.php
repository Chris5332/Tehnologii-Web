<?php

session_start();
header("Content-Type: application/json");


if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(["success" => false, "message" => "Access denied."]);
    exit;
}


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

$db->exec('CREATE TABLE IF NOT EXISTS "animals"(
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "name" TEXT NOT NULL,
    "birth_date" DATE NOT NULL,
    "species" TEXT NOT NULL,
    "breed" TEXT NOT NULL,
    "health_status" TEXT NOT NULL,
    "pickup_address" TEXT NOT NULL,
    "feeding_schedule" TEXT,
    "restrictions" TEXT,
    "relationship" TEXT,
    "description" TEXT,
    "is_group" INTEGER DEFAULT 0,
    "owner_id" INTEGER NOT NULL,
    "created_at" TIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(owner_id) REFERENCES users(id)
)');

$db->exec('CREATE TABLE IF NOT EXISTS "media"(
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "animal_id" INTEGER NOT NULL,
    "type" TEXT NOT NULL CHECK(type IN ("photo","video","audio")),
    "path" TEXT NOT NULL,
    FOREIGN KEY(animal_id) REFERENCES animals(id)
)');

$db->exec('CREATE TABLE IF NOT EXISTS "medical_history"(
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "animal_id" INTEGER NOT NULL,
    "description" TEXT NOT NULL,
    "treatment" TEXT,
    "date" DATE NOT NULL,
    FOREIGN KEY(animal_id) REFERENCES animals(id)
)');

echo json_encode(["success"=>true, "message"=>"Database structure initialized."]);

?>