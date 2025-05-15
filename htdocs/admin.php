<?php
include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/databaseConnection.php';

session_start();

    
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_unset();
    session_destroy();
    echo json_encode(['status' => 'logged_out']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styl.css">
</head>
<body>
</body>
<?php
    if(isset($_SESSION["login_email"])){
        $email = $_SESSION["login_email"];
        if($email != "Admin@admin.ad"){
            echo "<script> window.open('index.php', '_self');</script>";
            return;
        }
        echo "<script> $(`body`).html(`<div class='glownyDiv'><h1 id='header'>Witamy na stronie Administratora!</h1></div>`);</script>";
        $db = Database::getInstance();
        $conn = $db->conn;
        $sql = "SELECT imie, nazwisko, email FROM uzytkownicy WHERE email = '".$email."'";
        $res = mysqli_fetch_array(mysqli_query($conn, $sql));
        $dane= $res;
        mysqli_close($conn);
    }
    
?>
<script>
    var initdata;
    function przejdzDoLogowania(){
        window.open('logowanie.html', '_self');
    }

    function przejdzDoRejestracji(){
        window.open('rejestracja.html', '_self');
    }
    function showError(elementid, wiadomosc){
        hideError(elementid);
        $("#" + elementid).append("<span class='errorSpan' id='errorSpan" + elementid + "'><br>" + wiadomosc + "</span>");
    }
    function hideError(elementid){
        $("#errorSpan" + elementid ).remove();
    }
$(document).ready(()=>{
    if($("#niezalogowany").length == 0){
        initdata = JSON.parse(`<?php echo json_encode($dane); ?>`);
        var wysylac = false;
        $("#header").after(`
            <button id='removeUsersBtn'>Usunąć niezarejestrowanych użytkowników?</button><br>
            <button id="logoutBtn">wyloguj się</button>`);
        $("#removeUsersBtn").click(()=>{
            $.ajax({
                url: "usunUzytkownikow.php",
                type: "GET",
                success: function(data){
                    alert("Usunięto niezarejestrowanych użytkowników");
                },
                error: function(){
                    alert("Coś poszło nie tak");
                }
            });
        });
        $("#logoutBtn").click(()=>{
            $.ajax({
                url: "", // same file
                type: "POST",
                data: { action: "logout" },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status === "logged_out") {
                        alert("Zostałeś wylogowany!");
                        window.open("index.php","_self");
                    }
                }
            });
        });
        
    }
});
</script>

</html>