<?php
    include_once __DIR__ . '/../vendor/autoload.php';
    include_once __DIR__ . '/databaseConnection.php';
    session_start();
    $db = Database::getInstance();
    $conn = $db->conn;
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
    $result = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM uzytkownicy WHERE email='$email';"));
    
    if(empty($result)){
        echo "<div id='komunikat' class='glownyDiv'><h1>Potwierdz rejestracje wiadomością wysłaną na email</h1><br><h4>Gdy to zrobisz odświesz tą stronę</h4>".
        "<button id='btn1'>Wysłać email ponownie?</button></div>";
    }else{
        $_SESSION["login_email"] = $email;
        echo "<script> window.open('index.php','_self');</script>";
    }
    mysqli_close($conn);
?>
</body>
<script>
$(document).ready(()=>{
    sendEmail(false);
    $('#btn1').click(()=>{sendEmail(true);});
});
function sendEmail(resend){
    $.ajax({
        url: 'mail.php',
        type: 'POST',
        data: {
            email: '<?php echo $email;?>',
            reset: resend,
        }
        ,
        success: function(data){
            console.log(data);
            if(data == "Wysłaliśmy wiadomość"){
                alert("Wysłaliśmy nową wiadomość");
            }
        },
        error: function(){
            $("button").after("Mamy problem z serwerem");
        }
    });
}
</script>
</html>

