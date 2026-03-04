<?php
// check_db.php - simple connection verifier, returns JSON so UI can handle gracefully
header('Content-Type: application/json');

try {
    $cfg = require __DIR__ . '/db_config.php';
    $conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db']);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    $tables = [];
    if ($result = $conn->query("SHOW TABLES")) {
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
        $result->free();
    }
    echo json_encode(['success' => true, 'tables' => $tables]);
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
