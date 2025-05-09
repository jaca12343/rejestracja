<?php
    $email = $_GET["email"];
    $code = $_GET["code"];
    $conn = mysqli_connect("mysql1.ugu.pl","db701691", "Jacdom1234-u9u", "db701691");
    $result = mysqli_fetch_array(mysqli_query($conn,"SELECT kod_weryfikacyjny FROM rejestrowani_uzytkownicy WHERE email='".$email."';"));
    echo "<link rel='stylesheet' type='text/css' href='styl.css'><div class='glownyDiv'><h1>";
    if(empty($result)){
        echo "To konto jest juz zarejestrowane, lub czas na rejestracje minal";
    }else if($code != $result[0]){
        echo "niepoprawny kod weryfikacyjny";
    }else{
        mysqli_query($conn,"INSERT INTO uzytkownicy(email, haslo, zakodowane_haslo, imie, nazwisko) SELECT email, haslo, zakodowane_haslo, imie, nazwisko FROM rejestrowani_uzytkownicy WHERE email='".$email."';");
        mysqli_query($conn,"DELETE FROM rejestrowani_uzytkownicy WHERE email = '".$email."';");
        echo("Konto dodane<br>odswiez strone na ktorej sie logowales/as");
    }
    echo "</h1></div>";



?>