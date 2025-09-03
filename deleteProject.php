<?php
header('Access-Control-Allow-Origin: https://renadeveloper.altervista.org');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

require_once("db_connect.php");

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!isset($data['project_id'], $data['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Dati mancanti"]);
    exit;
}

try {
    $checkSql = "SELECT id FROM progetti WHERE id = :project_id AND autore = :user_id";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':project_id', $data['project_id']);
    $checkStmt->bindParam(':user_id', $data['user_id']);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(["status" => "error", "message" => "Progetto non trovato o non autorizzato"]);
        exit;
    }

    $deleteSql = "DELETE FROM progetti WHERE id = :project_id AND autore = :user_id";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bindParam(':project_id', $data['project_id']);
    $deleteStmt->bindParam(':user_id', $data['user_id']);
    
    if ($deleteStmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Progetto eliminato con successo"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Errore nell'eliminazione"]);
    }
    
} catch (PDOException $e) {
    error_log("Errore deleteProject.php: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Errore database: " . $e->getMessage()]);
}
?>
