<?php
header('Content-Type: application/json');

// pull connection info from centralized config
$cfg = require __DIR__ . '/db_config.php';
$conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db']);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit();
}

$conn->set_charset("utf8mb4");

// Check if enrollment_periods table exists and has active period
$result = $conn->query("SELECT * FROM enrollment_periods WHERE is_active = TRUE LIMIT 1");

if ($result && $result->num_rows > 0) {
    $period = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'message' => 'Database connected and active enrollment period found',
        'period' => $period
    ]);
} else {
    // Try to create an active enrollment period
    $school_year = "2025-2026";
    $semester = "First";
    
    $stmt = $conn->prepare("INSERT INTO enrollment_periods (school_year, semester, semester_start, semester_end, is_active) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH), TRUE)");
    
    if ($stmt) {
        $stmt->bind_param("ss", $school_year, $semester);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Database connected. Created active enrollment period automatically',
                'period_id' => $conn->insert_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database connected but cannot create enrollment period: ' . $stmt->error
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database connected but error: ' . $conn->error
        ]);
    }
}

$conn->close();
?>
