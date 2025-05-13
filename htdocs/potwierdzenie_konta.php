<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
    $db = Database::getInstance();
    $conn = $db->conn;
    
    $email = $_GET["email"];
    $code = $_GET["code"];
    $result = mysqli_fetch_array(mysqli_query($conn,"SELECT kod_weryfikacyjny FROM rejestrowani_uzytkownicy WHERE email='".$email."';"));
    echo "<link rel='stylesheet' type='text/css' href='styl.css'><div class='glownyDiv'><h1>";
    if(empty($result)){
        echo "To konto jest juz zarejestrowane, lub czas na rejestracje minal";
    }else if($code != $result[0]){
        echo "niepoprawny kod weryfikacyjny";
    }else{
        mysqli_query($conn,"INSERT INTO uzytkownicy(email, haslo, zakodowane_haslo, imie, nazwisko) SELECT email, haslo, zakodowane_haslo, imie, nazwisko FROM rejestrowani_uzytkownicy WHERE email='".$email."';");
        mysqli_query($conn,"DELETE FROM rejestrowani_uzytkownicy WHERE email = '".$email."';");
        echo("Konto dodane<br>odśwież stronę na ktorej się logowałeś/aś");
    }
    echo "</h1></div>";
?>
</body>
<script>
    setTimeout(() => {
        window.open("logowanie.html","_self");
    }, 3000);
</script>
</html>