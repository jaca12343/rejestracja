<?php

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/php/databaseConnection.php';

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
    <!-- -->
    <link rel="stylesheet" type="text/css" href="styl.css">
</head>
<body>
    <nav>
        <button id='showUsersBtn'>Wyświetl użytkowników</button><br>
        <button id='removeUsersBtn'>Usunąć niezarejestrowanych użytkowników?</button><br>
        <button id="logoutBtn">wyloguj się</button>
    </nav>
    <div class='glownyDiv'>
        <h1 id='header'>Witamy na stronie Administratora!</h1>
    </div>
    <div id="listaUzytkownikow" style="display: none;">
        <h1>Użytkownicy</h1>
        <h2>strona: 
            <select id="selectStrona">
            </select> 
            uzytkownicy na stronę: 
            <select id="linieNaStrone">
                <option>5</option>
                <option selected>10</option>
                <option>15</option>
                <option>20</option>
                <option>25</option>
            </select>
        </h2>
        <div id="filtry">
            <h4>Filtry:</h4>
            status:<select id="filtrZarejestrowany">
                <option value=0 style="background-color:rgb(255, 255, 255);">wszyscy</option>
                <option value=1 style="background-color:rgb(220, 255, 202);">zarejestrowani</option>
                <option value=2 style="background-color:rgb(255, 220, 220);">niezarejestrowani</option>
            </select>
        </div>
        <table id="UzytkownicyOl">

        </table><br>
        <button id="zablokujbtn">zablokuj</button>
        <button id="odblokujbtn">odblokuj</button>
        <button id="usunbtn">usuń</button>
        <p id="divPotwierdzenie"></p>
    </div>
    <footer>
        <a href="https://www.flaticon.com/free-icons/sorting" title="sorting icons">Sorting icons created by Freepik - Flaticon</a>
        <a href="https://www.flaticon.com/free-icons/reload" title="reload icons">Reload icons created by mavadee - Flaticon</a>
    </footer>
</body>
<?php
    if(isset($_SESSION["login_email"])){
        $email = $_SESSION["login_email"];
        if($email != "Admin@admin.ad"){
            echo "<script> window.open('index.php', '_self');</script>";
            return;
        }
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
    var usersData;
    var dataBeforeFilters;
    var linesPerPage = 10;
    var currentPage = 0;
    var checkboxes = new Array();
    var lastSorted;
    var sortLevel;
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

        $("#showUsersBtn").click(()=>{
            getData();
        });

        $("#removeUsersBtn").click(()=>{
            $.ajax({
                url: "/htdocs/php/usunUzytkownikow.php",
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
        $("#linieNaStrone").change(function(){
            linesPerPage = $(this).find("option:selected").text();
            configurePages();
            changePages(0);
        });
        $("#filtrZarejestrowany").change(function(){
            applyFilters();
        });
        $("#zablokujbtn").click(()=>{
            blockUsers();
        });
        $("#odblokujbtn").click(()=>{
            unblockUsers();
        });
        $("#usunbtn").click(()=>{
            deleteUsers();
        });
    }
});
function getData(){
    $.ajax({
        url: "/htdocs/php/wybierzUzytkownikow.php", // same file
        type: "POST",
        data: {},
        success: function(data) {
            dataBeforeFilters = data;
            usersData = dataBeforeFilters.map(obj => ({ ...obj }));
            
            showRecords();
            configurePages();
        }
    });
}
function configurePages(){
    $("#selectStrona").children().remove();
    for(let i = 0; i < usersData.length / linesPerPage; i++){
        $("#selectStrona").append(`<option>${i+1}</option>`);
        if(i == currentPage)
            $("#selectStrona option").last().prop('selected', true);
    }
    if($("#selectStrona option").first().prop("selected") == true){
        currentPage = 0;
    }
    $("#selectStrona").change(function(){
        var value = $(this).find("option:selected").text() - 1;
        changePages(value);
    });
}
function changePages(page){
    currentPage = page;
    showRecords();
}
function applyFilters(){
    $("#filtrZarejestrowany").css("background-color", $("#filtrZarejestrowany").find(":selected").css("background-color"));
    switch($("#filtrZarejestrowany").find(":selected").val()){

        case '1':
            usersData=[];
            
            dataBeforeFilters.forEach(element => {
                if(element["kod_weryfikacyjny"] == null){
                    usersData.push({...element});
                }
            });
        break;
        case '2':
            usersData=[];
            dataBeforeFilters.forEach(element => {
                if(element["kod_weryfikacyjny"] != null){
                    usersData.push({...element});
                }
            });
            break;
        default:
            
            usersData=[];
            usersData = dataBeforeFilters.map(obj => ({ ...obj }));
            //console.log(usersData);
            //console.log(dataBeforeFilters);
            break;
    }
    configurePages();
    showRecords();
}
function callSorting(column, asc){
    quickSort(usersData, 0, usersData.length-1, column, asc);

    configurePages();
    showRecords();
}
function partition(arr, low, high, column, asc)
{

    // Choose the pivot
    let pivot = arr[high][column];

    // Index of smaller element and indicates
    // the right position of pivot found so far
    let i = low - 1;

    // Traverse arr[low..high] and move all smaller
    // elements to the left side. Elements from low to
    // i are smaller after every iteration
    for (let j = low; j <= high - 1; j++) {
        let a = arr[j][column];
        let b = pivot;
        a = a != null ? a.toString() : "";
        b = b != null ? b.toString() : "";

        let comparison = a.localeCompare(b, 'pl', {sensitivity: 'base', numeric: true});

        if ((comparison < 0 && asc) || (comparison > 0 && !asc)) {
            i++;
            swap(arr, i, j);
        }
    }

    // Move pivot after smaller elements and
    // return its position
    swap(arr, i + 1, high);
    return i + 1;
}
function swap(arr, i, j)
{
    let temp = arr[i];
    arr[i] = arr[j];
    arr[j] = temp;
}
function quickSort(arr, low, high, column, asc)
{
    if (low < high) {

        // pi is the partition return index of pivot
        let pi = partition(arr, low, high, column, asc);

        // Recursion calls for smaller elements
        // and greater or equals elements
        quickSort(arr, low, pi - 1, column, asc);
        quickSort(arr, pi + 1, high, column, asc);
    }
}
function decoupleCheckboxes(){
    
    let registered_Users = new Array();
    let unregistered_Users = new Array();
    
    
    let regex1 = /checkbox_z/;
    let regex2 = /checkbox_n/;
    checkboxes.forEach(element => {
        if(regex1.test(element)){
            registered_Users.push(element.slice(10));
        }else if(regex2.test(element)){
            unregistered_Users.push(element.slice(10));
        }else{
            console.log("Nieoczekiwane id");
        }
    });
    let returned_array = {};
    returned_array['registered_Users'] = registered_Users;
    returned_array['unregistered_Users'] = unregistered_Users;
    return returned_array;
}
function blockUsers(){
    if(checkboxes.length <= 0)
        return;
    if(confirm(`Czy na pewno chcesz zablokować ${checkboxes.length} użytkowników?`)){
        let myArray = decoupleCheckboxes();
        $.ajax({
            url: "php/blockUsers.php",
            type: "POST",
            data: {
                registered_Users: myArray['registered_Users'],
                unregistered_Users: myArray['unregistered_Users']
            },
            success(data){
                $("#divPotwierdzenie").html(data);
                getData();
                alert("udało się zablokować użytkowników");
            }
        })
    }
}
function unblockUsers(){
    if(checkboxes.length <= 0)
        return;
    if(confirm(`Czy na pewno chcesz odblokować ${checkboxes.length} użytkowników?`)){
        let myArray = decoupleCheckboxes();
        $.ajax({
            url: "php/unblockUsers.php",
            type: "POST",
            data: {
                registered_Users: myArray['registered_Users'],
                unregistered_Users: myArray['unregistered_Users']
            },
            success(data){
                $("#divPotwierdzenie").html(data);
                getData();
                alert("udało się odblokować użytkowników");
            }
        })
    }
}
function deleteUsers(){
    if(checkboxes.length <= 0)
        return;
    if(confirm(`Czy na pewno chcesz usunąć ${checkboxes.length} użytkowników?`)){
        let myArray = decoupleCheckboxes();
        $.ajax({
            url: "php/deleteUsers.php",
            type: "POST",
            data: {
                registered_Users: myArray['registered_Users'],
                unregistered_Users: myArray['unregistered_Users']
            },
            success(data){
                $("#divPotwierdzenie").html(data);
                getData();
                alert("udało się usunąć użytkowników");
                checkboxes = [];
            }
        })
    }
}
//Sprawdznie jak ostatnio było sortowane
function configureSorting(element){
    if(lastSorted != $(element).val()){
        lastSorted = $(element).val();
        sortLevel = 1;
        callSorting(lastSorted, true);
    }else{
        sortLevel++;
        if(sortLevel == 1){
            callSorting(lastSorted, true);
        }else if(sortLevel == 2){
            callSorting(lastSorted, false);
        }else{
            sortLevel = 0;
            applyFilters();
        }
    }
}
function showRecords(){
    let kolumnyNapis = ["Id", "imię", "nazwisko", "email", "hasło", "zablokowany"];
    let kolumnyJS = ["id", "imie", "nazwisko", "email", "haslo", "zablokowany"]

    $("#UzytkownicyOl").children().remove();
    $("#UzytkownicyOl").append(`<tr><th>numer</th></tr>`);
    for(let i = 0; i < kolumnyNapis.length; i++){
        $("#UzytkownicyOl tr").append($(`<th>${kolumnyNapis[i]}&nbsp&nbsp<button class='sortButton' value='${kolumnyJS[i]}'></button></th>`));
    }
    $(".sortButton").css("background-image", "url('images/sort.png')");
    $(".sortButton").each(function(){
        if($(this).val() == lastSorted){
            if(sortLevel == 1){
                $(this).css("background-image", "url('images/sortDown.png')");
            }else if(sortLevel == 2){
                $(this).css("background-image", "url('images/sortUp.png')");
            }
        }
    });
    $(".sortButton").click(function(){
        configureSorting(this);
    });
    $(".glownyDiv").css("display", "none")
    $("#listaUzytkownikow").css("display","block");
    var trClass;
    for(let i = currentPage * linesPerPage; i < (currentPage + 1) * linesPerPage; i++){
        if(i >= usersData.length){
            break;
        }
        if(usersData[i]["kod_weryfikacyjny"] == null){
            trClass = "zarejestrowany";
            cBoxId = "z";
        }else{
            trClass = "niezarejestrowany";
            cBoxId = "n";
        }
        $("#UzytkownicyOl").append(`<tr class='${trClass}'><td><input type="checkbox" id="checkbox_${cBoxId}${usersData[i]["id"]}"> ${i}:</td></tr>`);
        for(let j = 0; j < kolumnyJS.length; j++){
            $("#UzytkownicyOl").children().last().append(`<td>${usersData[i][kolumnyJS[j]]}</td>`);
        }
        if(checkboxes.includes(`checkbox_${cBoxId}${usersData[i]["id"]}`)){
            document.getElementById(`checkbox_${cBoxId}${usersData[i]["id"]}`).checked = true;
        }
    }
    $("input[type='checkbox']").click(function(){manageCheckbox(this)});
}
function manageCheckbox(element){
    if($(element).is(":checked")){
        checkboxes.push($(element).attr("id"));
    }else{
        let index = checkboxes.indexOf($(element).attr("id"));
        if (index !== -1) {
            checkboxes.splice(index, 1);
        }
    }
}
</script>

</html>