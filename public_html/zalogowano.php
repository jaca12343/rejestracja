<?php
session_start();
$email = $_SESSION["login_email"];
$conn = mysqli_connect("mysql1.ugu.pl","db701691", "Jacdom1234-u9u", "db701691");
$sql = "SELECT imie, nazwisko, email FROM uzytkownicy WHERE email = '".$email."'";
$res = mysqli_fetch_array(mysqli_query($conn, $sql));
$dane= $res;

mysqli_close($conn);
echo json_encode($dane);


?>