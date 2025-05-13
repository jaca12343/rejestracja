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
        $("#header").after(`<p id='informacje'>Twoje imie: <span class='dane' id='imie'>${initdata.imie}</span><br>
                Twoje nazwisko: <span class='dane' id='nazwisko'>${initdata.nazwisko}</span><br>
                Twój email:${initdata.email}</p>
                <button id='changeDataBtn'>Chcesz zmienić swoje dane?</button><br>
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
        $("#changeDataBtn").click(()=>{
            if($("#changeDataBtn").text() == "Chcesz zmienić swoje dane?"){
                spanToInput();
            }else{
                var changed = false;
                if(!(initdata.imie == $("#imie").val() && initdata.nazwisko == $("#nazwisko").val())){
                    $.ajax({
                        url: "zmienDane.php", // same file
                        type: "POST",
                        data: { 
                            imie: $("#imie").val(),
                            nazwisko: $("#nazwisko").val(),
                            email: initdata.email,
                        },
                        success: function(data) {
                            console.log(data);
                            data = JSON.parse(data);
                            if(data == -1){
                                showError("informacje","Te dane są już zajęte");
                            }else{
                                hideError("informacje");
                                initdata.imie = $("#imie").val();
                                initdata.nazwisko = $("#nazwisko").val();
                                changed = true;
                                inputToSpan();
                                alert("Dane zostały zapisane");
                            }
                        }
                    });
                }else{
                    hideError("informacje");
                    inputToSpan();
                }   
            }
        });
    }
});
function inputToSpan(){
    $("#changeDataBtn").text("Chcesz zmienić swoje dane?");
    $(".daneZ").each(function(){
        $(this).replaceWith("<span class='dane' id='" + $(this).attr('id') + "'>" + $(this).val() + "</span>");
    });
    $("#cancelBtn").remove();
}
function spanToInput(){
    $("#changeDataBtn").text("Zapisz zmiany");
    $("#changeDataBtn").before("<button id='cancelBtn' onclick='anuluj()'>Anuluj</button>");
    $(".dane").each(function(){
        $(this).replaceWith("<input class='daneZ' id='" + $(this).attr('id') + "' value='" + $(this).text() + "'>");
    });
}
function anuluj(){
    $("#imie").val(initdata.imie);
    $("#nazwisko").val(initdata.nazwisko);
    inputToSpan();
}
</script>

</html>