<?php
    include_once __DIR__ . '/databaseConnection.php';

    $db = Database::getInstance();
    $conn = $db->conn;

    mysqli_query($conn,"DELETE FROM rejestrowani_uzytkownicy;");

?>