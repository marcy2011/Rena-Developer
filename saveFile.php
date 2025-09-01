<?php
header('Access-Control-Allow-Origin: https://renadeveloper.altervista.org');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Cache-Control, Pragma, Expires');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

error_log("SaveFile request received: " . file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

require_once("db_connect.php");

error_log("SaveFile request received: " . file_get_contents("php://input"));

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "JSON invalido: " . json_last_error_msg()]);
    exit;
}

if (!isset($data['titolo'], $data['descrizione'], $data['linguaggio'], $data['autore'])) {
    echo json_encode(["status" => "error", "message" => "Dati mancanti", "received" => $data]);
    exit;
}

try {
    $checkSql = "SELECT id FROM progetti WHERE titolo = :titolo AND autore = :autore";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':titolo', $data['titolo']);
    $checkStmt->bindParam(':autore', $data['autore']);
    $checkStmt->execute();
    
    $existingProject = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingProject) {
        if (isset($data['id']) && $data['id'] == $existingProject['id']) {
            $sql = "UPDATE progetti SET titolo = :titolo, descrizione = :descrizione, linguaggio = :linguaggio, data_creazione = CURRENT_TIMESTAMP WHERE id = :id AND autore = :autore";
            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(':titolo', $data['titolo']);
            $stmt->bindParam(':descrizione', $data['descrizione']);
            $stmt->bindParam(':linguaggio', $data['linguaggio']);
            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':autore', $data['autore']);
            
            if ($stmt->execute()) {
                $lastId = $data['id'];
                error_log("Project updated successfully with ID: " . $lastId);
                echo json_encode([
                    "status" => "success", 
                    "message" => "Progetto aggiornato", 
                    "id" => $lastId,
                    "project" => [
                        "id" => $lastId,
                        "titolo" => $data['titolo'],
                        "descrizione" => $data['descrizione'],
                        "linguaggio" => $data['linguaggio'],
                        "autore" => $data['autore']
                    ]
                ]);
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Error in updating: " . implode(", ", $errorInfo));
                echo json_encode(["status" => "error", "message" => "Errore nell'aggiornamento: " . $errorInfo[2]]);
            }
        } else {
            $sql = "UPDATE progetti SET descrizione = :descrizione, linguaggio = :linguaggio, data_creazione = CURRENT_TIMESTAMP WHERE id = :id AND autore = :autore";
            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(':descrizione', $data['descrizione']);
            $stmt->bindParam(':linguaggio', $data['linguaggio']);
            $stmt->bindParam(':id', $existingProject['id']);
            $stmt->bindParam(':autore', $data['autore']);
            
            if ($stmt->execute()) {
                $lastId = $existingProject['id'];
                error_log("Existing project updated with ID: " . $lastId);
                echo json_encode([
                    "status" => "success", 
                    "message" => "Progetto esistente aggiornato", 
                    "id" => $lastId,
                    "project" => [
                        "id" => $lastId,
                        "titolo" => $data['titolo'],
                        "descrizione" => $data['descrizione'],
                        "linguaggio" => $data['linguaggio'],
                        "autore" => $data['autore']
                    ]
                ]);
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Error in updating existing: " . implode(", ", $errorInfo));
                echo json_encode(["status" => "error", "message" => "Errore nell'aggiornamento: " . $errorInfo[2]]);
            }
        }
    } else {
        $sql = "INSERT INTO progetti (titolo, descrizione, linguaggio, autore) VALUES (:titolo, :descrizione, :linguaggio, :autore)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':titolo', $data['titolo']);
        $stmt->bindParam(':descrizione', $data['descrizione']);
        $stmt->bindParam(':linguaggio', $data['linguaggio']);
        $stmt->bindParam(':autore', $data['autore']);
        
        if ($stmt->execute()) {
            $lastId = $conn->lastInsertId();
            error_log("New project saved successfully with ID: " . $lastId);
            echo json_encode([
                "status" => "success", 
                "message" => "Nuovo progetto salvato", 
                "id" => $lastId,
                "project" => [
                    "id" => $lastId,
                    "titolo" => $data['titolo'],
                    "descrizione" => $data['descrizione'],
                    "linguaggio" => $data['linguaggio'],
                    "autore" => $data['autore']
                ]
            ]);
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("Error in saving new: " . implode(", ", $errorInfo));
            echo json_encode(["status" => "error", "message" => "Errore nel salvataggio: " . $errorInfo[2]]);
        }
    }
} catch (PDOException $e) {
    error_log("Database error in saveFile.php: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Errore database: " . $e->getMessage()]);
} catch (Exception $e) {
    error_log("General error in saveFile.php: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Errore generico: " . $e->getMessage()]);
}
?>
