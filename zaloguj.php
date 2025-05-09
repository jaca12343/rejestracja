<?php
session_start();
$email = $_POST['email'];
$givenPassword = $_POST['password'];
$conn = mysqli_connect("mysql1.ugu.pl","db701691", "Jacdom1234-u9u", "db701691");
$sql = "SELECT zakodowane_haslo FROM uzytkownicy WHERE email = '".$email."'";
$res = mysqli_query($conn, $sql);
$correctPassword= mysqli_fetch_array($res);
mysqli_close($conn);
if(empty($correctPassword[0])){
    echo json_encode(-1);
}else if($correctPassword[0] == hash("sha256", $givenPassword)){
    $_SESSION["login_email"] = $email;
    echo json_encode(0);
}else{
    echo json_encode(-2);
}

?>