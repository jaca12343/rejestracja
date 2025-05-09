<?php
    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $conn = mysqli_connect("mysql1.ugu.pl","db701691", "Jacdom1234-u9u", "db701691");
    $data = array();
    $result = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM uzytkownicy WHERE email='".$email."'"));
    //jeżeli istnieje już taki email
    if(!empty($result)){
        $data[0] = -1;
        $data[1] = "email zajety";
        echo json_encode($data);
        mysqli_close($conn);
        return;
    }
    $result = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM uzytkownicy WHERE imie='".$name."' AND nazwisko='".$surname."'"));
    //jeżeli te imie i nazwisko jest już zajęte
    if(!empty($result)){
        $data[0] = -2;
        $data[1] = "imie i nazwisko zajete";
        echo json_encode($data);
        mysqli_close($conn);
        return;
    }else{
        //jeśli wszystko jest dobrze
        $data[0] = 0;
        $data[1] = "Wszystko dobrze";
        //jezeli konto nie oczekuje na dodanie to dodaj do kolejki
        $result = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM rejestrowani_uzytkownicy WHERE email='".$email."';"));
        if(empty($result)){
            //losowanie liczby od 0 do miliarda
            $verificationNumber = rand(0,1000000000);
            $hashpassword = hash("sha256",$password);
            mysqli_query($conn, "INSERT INTO rejestrowani_uzytkownicy (email, haslo, zakodowane_haslo, imie, nazwisko, kod_weryfikacyjny) VALUES ('$email', '$password', '$hashpassword', '$name', '$surname', $verificationNumber);");
        }else{
            $r = mysqli_fetch_array(mysqli_query($conn, "SELECT kod_weryfikacyjny FROM rejestrowani_uzytkownicy WHERE email = '$email'"));
            $verificationNumber = $r[0];
        }
        $msg = "Ktoś próbuje się zalogować do naszego serwisu za pomocą twojego adresu e-mail kliknij tutaj jeśli to ty: 'http://jaca15.ugu.pl/potwierdzenie_konta.php?email=$email&code=$verificationNumber";

        $to = $email;
        $email = "jaca15@jaca15.ugu.pl";
        $subject = "Potwierdź założenie konta";
        $msg = wordwrap($msg,70);

        //Send email
        mail($to, $subject, $msg, "From:". $email);

        //Email response
        

        // send email

        echo json_encode($data);
        mysqli_close($conn);
        return;
    }

?>