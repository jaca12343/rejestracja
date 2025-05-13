<?php
    include_once __DIR__ . '/../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv -> load();
    $db_host = $_ENV["DB_HOST"];
    $db_user = $_ENV["DB_USER"];
    $db_pass = $_ENV["DB_PASS"];
    $db_name = $_ENV["DB_NAME"];
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    session_start();
    $email = $_SESSION["login_email"];
    $sql = "SELECT imie, nazwisko, email FROM uzytkownicy WHERE email = '".$email."'";
    $res = mysqli_fetch_array(mysqli_query($conn, $sql));
    $dane= $res;

    mysqli_close($conn);
    echo json_encode($dane);


?>