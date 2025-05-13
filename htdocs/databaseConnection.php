<?php
include_once __DIR__ . '/../vendor/autoload.php';
class Database{
    private static $instance =null;
    public $conn;
    
    //konstruktor
    private function __construct(){
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv -> load();
        $host = $_ENV["DB_HOST"];
        $user = $_ENV["DB_USER"];
        $pass = $_ENV["DB_PASS"];
        $name = $_ENV["DB_NAME"];

        $this->conn = mysqli_connect($host, $user, $pass, $name);
    }

    private function __clone() {}
    public function __wakeup() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getConnection() {
        return $this->conn;
    }
    public function getZakodowaneHaslo($email){
        return mysqli_fetch_array(mysqli_query($this->conn,"SELECT zakodowane_haslo FROM uzytkownicy WHERE email = '$email'"));
    }
}
?>