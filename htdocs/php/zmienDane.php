<?php

    include_once __DIR__ . '/../../vendor/autoload.php';
    include_once __DIR__ . '/databaseConnection.php';
    $db = Database::getInstance();
    $conn = $db->conn;
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $email = $_POST['email'];
    $res = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM uzytkownicy WHERE imie='$imie' AND nazwisko='$nazwisko'"));
    if(!empty($res)){
        echo json_encode(-1);
        return;
    }
    mysqli_query($conn, "UPDATE uzytkownicy SET imie = '$imie', nazwisko = '$nazwisko' WHERE email = '$email'; ");
    echo json_encode(0);
    return;
?>