<?php
$host = "localhost";
$user = "rena";
$pass = "PASS";
$dbname = "my_rena";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    error_log("Connessione al database fallita: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Connessione al database fallita"]);
    exit;
}
?>
