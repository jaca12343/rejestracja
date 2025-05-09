<?php
    session_start();

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdzenie rejestracji</title>
    <link rel="stylesheet" type="text/css" href="styl.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
<?php
    $email = $_POST["email"];
    $conn = mysqli_connect("mysql1.ugu.pl","db701691", "Jacdom1234-u9u", "db701691");
    $result = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM uzytkownicy WHERE email='$email';"));
    
    if(empty($result)){
        echo "<div id='komunikat' class='glownyDiv'><h1>Potwierdz rejestracje wiadomością wysłaną na email</h1></div>";
    }else{
        $_SESSION["login_email"] = $email;
        echo "<script> window.open('index.php','_self');</script>";
    }
    mysqli_close($conn);
?>
</body>
</html>

