<?php
    include_once __DIR__ . '/../../vendor/autoload.php';
    include_once __DIR__ . '/databaseConnection.php';
    $db = Database::getInstance();
    $conn = $db->conn;

    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $data = array();

    //walidacja bez udzialu bazy ->
    $passwordRegexp = '/^(?=.{6,})(?=.*[A-Z])(?=.*[a-z])(?=.*[!-\x2F:-@\x5B-\x60\x7B-~]).*$/';
    $emailregexp = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    $connectWithDB = true;
    if(!preg_match($passwordRegexp, $password)){
        $data=[-2, "Niepoprawne hasło"];
        $connectWithDB = false;
    }else if(!preg_match($emailregexp, $email)){
        $data=[-1, "Niepoprawny email"];
        $connectWithDB = false;
    }else if(empty($name)){
        $data=[-3, "Nie podano imienia"];
        $connectWithDB = false;
    }else if(empty($surname)){
        $data=[-4, "Nie podano nazwiska"];
        $connectWithDB = false;
    }
    if($connectWithDB == false){
        echo json_encode($data);
        return;
    }


    //walidacja z baza ->
    $result = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM uzytkownicy WHERE email='".$email."'"));
    //jeżeli istnieje już taki email
    if(!empty($result)){
        $data=[-1, "Email zajety"];
        echo json_encode($data);
        return;
    }
    $result = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM uzytkownicy WHERE imie='".$name."' AND nazwisko='".$surname."'"));
    //jeżeli te imie i nazwisko jest już zajęte
    if(!empty($result)){
        $data = [-4, "Imie i nazwisko zajete"];
        echo json_encode($data);
        return;
    }else{
        //jeśli wszystko jest dobrze
        $data = [0, "Wszystko dobrze"];
        //jezeli konto nie oczekuje na dodanie to dodaj do kolejki
        $result = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM rejestrowani_uzytkownicy WHERE email='".$email."';"));
        $hashpassword = hash("sha256",$password);
        if(empty($result)){
            //losowanie liczby od 0 do miliarda
            $verificationNumber = rand(0,1000000000);
            mysqli_query($conn, "INSERT INTO rejestrowani_uzytkownicy (email, haslo, zakodowane_haslo, imie, nazwisko, kod_weryfikacyjny) VALUES ('$email', '$password', '$hashpassword', '$name', '$surname', $verificationNumber);");
        }else{
            mysqli_query($conn, "UPDATE rejestrowani_uzytkownicy SET haslo='$password', zakodowane_haslo='$hashpassword', imie='$name', nazwisko='$surname' WHERE email='$email';");
            $r = mysqli_fetch_array(mysqli_query($conn, "SELECT kod_weryfikacyjny FROM rejestrowani_uzytkownicy WHERE email = '$email'"));
            $verificationNumber = $r[0];
        }
        echo json_encode($data);
        return;
    }

?>