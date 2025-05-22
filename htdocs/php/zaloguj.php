<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . '/databaseConnection.php';

session_start();

$sb = Database::getInstance();

$email = $_POST['email'];
$givenPassword = $_POST['password'];

$emailregexp = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}\x24/";

$correctPassword = Database::getInstance()->getZakodowaneHaslo($email);

//$dbaction = $conn ->prepare("SELECT zakodowane_haslo FROM uzytkownicy WHERE email = ':email'")
//$dbaction->execute(['email' => $email]);
//$correctPassword= $dbaction ->fetch(PDO::FETCH_ASSOC);

if(empty($givenPassword)){
    $data = [-1, "Podaj hasło"];
}else if(!preg_match($emailregexp, $email)){
    $data = [-2, "Niepoprawny email"];
}else if(empty($correctPassword[0])){
    $data = [-2, "Nie ma takiego użytkownika"];
}else if($correctPassword[0] != hash("sha256", $givenPassword)){
    $data = [-1, "Niepoprawna nazwa użytkownika lub hasło"];
}else{
    $_SESSION["login_email"] = $email;
    $data = [0, "wszystko dobrze"];
}
echo json_encode($data);

?>