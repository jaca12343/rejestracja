<?php

    include_once __DIR__ . '/databaseConnection.php';

    $db = Database::getInstance();
    $conn = $db->conn;
    $registeredUsers = $_POST["registered_Users"];
    $unregisteredUsers = $_POST["unregistered_Users"];
    header("charset=utf-8");
    echo "usunięto następujące emaile:";
    foreach ($registeredUsers as $user) {
        echo "<br>".mysqli_fetch_array(mysqli_query($conn, "SELECT email FROM `uzytkownicy` WHERE `id` = $user;"))['email'];
        mysqli_query($conn, "DELETE FROM `uzytkownicy` WHERE `id` = $user;");
    }
    foreach ($unregisteredUsers as $user) {
        echo "<br>".mysqli_fetch_array(mysqli_query($conn, "SELECT email FROM `rejestrowani_uzytkownicy` WHERE `id` = $user;"))['email'];
        mysqli_query($conn, "DELETE FROM `rejestrowani_uzytkownicy` WHERE `id` = $user;");
    }



?>