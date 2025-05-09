<?php
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
    <div class='glownyDiv' id="niezalogowany">
        <h1>Witamy na naszej stronie! </h1>
        <span class='pytanie'>Byłeś tu już? </span><button onclick='przejdzDoLogowania()'>zaloguj się</button><br><br>
        <span class='pytanie'>Pierwszy raz u nas? </span><button onclick='przejdzDoRejestracji()'>zarejestruj się</button>
    </div>
</body>
<?php
    if(isset($_SESSION["login_email"])){
        $email = $_SESSION["login_email"];
        echo "<script> $(`#niezalogowany`).after(`<div class='glownyDiv'><h1 id='header'>Brawo udało ci się zalogować!</h1></div>`);   $('#niezalogowany').remove(); </script>";
        $conn = mysqli_connect("mysql1.ugu.pl","db701691", "Jacdom1234-u9u", "db701691");
        $sql = "SELECT imie, nazwisko, email FROM uzytkownicy WHERE email = '".$email."'";
        $res = mysqli_fetch_array(mysqli_query($conn, $sql));
        $dane= $res;
        mysqli_close($conn);
    }
    
?>
<script>
    function przejdzDoLogowania(){
        window.open('logowanie.html', '_self');
    }

    function przejdzDoRejestracji(){
        window.open('rejestracja.html', '_self');
    }
$(document).ready(()=>{
    //if($("#niezalogowany").length() == 0){
        
        let data = JSON.parse(`<?php echo json_encode($dane); ?>`);
        
        console.log(data);
        $("#header").after(`<p id='informacje'>Twoje imie: ${data.imie}<br>
                Twoje nazwisko: ${data.nazwisko} <br>
                Twój email: ${data.email}</p>
                <button id="logoutBtn">wyloguj się</button>`);
        $("#logoutBtn").click(()=>{
            $.ajax({
                url: "", // same file
                type: "POST",
                data: { action: "logout" },
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status === "logged_out") {
                        alert("Zostałeś wylogowany!");
                        location.reload();
                    }
                }
            });
        });
    //}
});
</script>

</html>