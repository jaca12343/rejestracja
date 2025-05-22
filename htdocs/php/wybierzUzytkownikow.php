<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    include_once __DIR__ . '/databaseConnection.php';

    $db = Database::getInstance();
    $conn = $db->conn;
    mysqli_set_charset($conn, "utf8");
    header('Content-Type: application/json; charset=utf-8');
    $result = mysqli_query($conn,"SELECT * FROM uzytkownicy");
    echo "[";
    //zarejestrowani użytkownicy
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
        while ($row = $result->fetch_assoc()) {
            echo ",".json_encode($row);  // Push each row into the array
        }
    } else {
        echo "upsik";
    }

    $result = mysqli_query($conn,"SELECT * FROM rejestrowani_uzytkownicy");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo ",".json_encode($row);  // Push each row into the array
        }
    } else {
        echo "upsik";
    }
    echo "]";
?>

