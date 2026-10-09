<?php
header('Content-Type: application/json');
require_once("db_connect.php"); 
header('Access-Control-Allow-Origin: https://renadeveloper.altervista.org');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if (!isset($_GET['autore'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Autore mancante"]);
    exit;
}

$autore = $_GET['autore'];

try {
    $sql = "SELECT id, titolo, descrizione, linguaggio, data_creazione 
            FROM progetti WHERE autore = :autore ORDER BY data_creazione DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':autore', $autore);
    $stmt->execute();
    
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "status" => "success", 
        "count" => count($projects),
        "projects" => $projects
    ]);
    
} catch (PDOException $e) {
    error_log("Errore getProjects.php: " . $e->getMessage());
    echo json_encode([
        "status" => "error", 
        "message" => "Errore nel recupero progetti"
    ]);
}
?>
