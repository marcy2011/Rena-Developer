<?php
header("Content-Type: application/json; charset=UTF-8");

$dir = __DIR__ . "/sharepreview/";

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

if (!empty($_POST['content'])) {
    $code = $_POST['content'];

    $filename = "share_" . time() . ".html";
    $filepath = $dir . $filename;

    if (file_put_contents($filepath, $code) !== false) {
        $url = "sharepreview/" . $filename;

        echo json_encode([
            "success" => true,
            "message" => "Condivisione generata",
            "url" => $url
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Errore nel salvataggio del file",
            "url" => ""
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Nessun contenuto fornito",
        "url" => ""
    ]);
}
