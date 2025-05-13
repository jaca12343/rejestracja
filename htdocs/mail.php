<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    include_once __DIR__ . '/../vendor/autoload.php'; 
    include_once __DIR__ . '/databaseConnection.php';
    session_start();


    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv -> load();
    $db = Database::getInstance();
        $conn = $db->conn;

    $mail_adress = $_ENV["MAIL_EMAIL"];
    $mail_host = $_ENV["MAIL_HOST"];
    $mail_password = $_ENV["MAIL_PASS"];
    $email = $_POST['email'];

    if(!isset($_SESSION['emailSent'])||$_POST['reset'] == 'true'||$_SESSION['emailSent']!=$email){
        
        $_SESSION['emailSent'] = $email;
        
        $r = mysqli_fetch_array(mysqli_query($conn, "SELECT kod_weryfikacyjny FROM rejestrowani_uzytkownicy WHERE email = '$email'"));
        $verificationNumber = $r[0];

        $mail = new PHPMailer(true); // Enable exceptions

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = $mail_host; // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = $mail_adress; // Your Mailtrap username
        $mail->Password = $mail_password; // Your Mailtrap password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender and recipient settings
        $mail->setFrom($mail_adress, 'Jaca15');
        $mail->addAddress($email);


        $mail->isHTML(false); 
        $mail->Subject = 'Potwierdzenie rejestracji';
        $mail->Body    = "Ktoś próbuje się zalogować do naszego serwisu za pomocą twojego adresu e-mail kliknij tutaj jeśli to ty: 'http://jacek-dombrowski.wuaze.com/htdocs/potwierdzenie_konta.php?email=$email&code=$verificationNumber";;

        // Send the email
        try{
            echo "Wysłaliśmy wiadomość";
            $mail->send();
        }catch(Eception $e){
            echo "coś poszło nie tak";
        }
    }else{
        echo 'Wiadomość już została wysłana';
    }
?>