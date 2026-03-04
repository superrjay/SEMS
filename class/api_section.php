<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

try {
    $cfg = require __DIR__ . '/db_config.php';
    $conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db']);
    if ($conn->connect_error) throw new Exception('DB: ' . $conn->connect_error);
    $conn->set_charset("utf8mb4");

    // GET SECTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getSections') {
        $query = "SELECT s.*, sub.subject_name, sub.subject_code, sub.subject_type,
              r.room_number AS room_number
              FROM sections s
              LEFT JOIN subjects sub ON s.subject_id = sub.subject_id
              LEFT JOIN rooms r ON s.room_id = r.room_id
              ORDER BY s.section_id DESC";
        $result = $conn->query($query);
        $sections = [];
        while ($row = $result->fetch_assoc()) {
            $sections[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $sections]);
        exit;
    }

    // SAVE SECTION
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'saveSection') {
        $sec_name = trim($_POST['sec_name'] ?? '');
        $sec_sub = trim($_POST['sec_sub'] ?? '');
        $sec_cat = trim($_POST['sec_cat'] ?? '');
        $sec_year = trim($_POST['sec_year'] ?? '');

        if (empty($sec_name)) {
            echo json_encode(['success' => false, 'message' => 'Section name is required']);
            exit;
        }

        // treat the entire submitted subject string as one entry
        // (may contain pipe separators if the user selected a grouped line)
        $subj = $sec_sub;
        $subject_id = null;
        $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_name = ?");
        $stmt->bind_param("s", $subj);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $code = strtoupper(substr($subj, 0, 3)) . date('His');
            $ins = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, subject_type, units) VALUES (?, ?, ?, 3)");
            $ins->bind_param("sss", $code, $subj, $sec_cat);
            $ins->execute();
            $subject_id = $conn->insert_id;
            $ins->close();
        } else {
            $subject_id = $result->fetch_assoc()['subject_id'];
        }
        $stmt->close();

        // Check/create period
        $pstmt = $conn->prepare("SELECT period_id FROM enrollment_periods WHERE is_active = TRUE ORDER BY period_id DESC LIMIT 1");
        $pstmt->execute();
        $presult = $pstmt->get_result();
        if ($presult->num_rows == 0) {
            $sy = date('Y') . '-' . (date('Y') + 1);
            $sem = "1st Semester";
            $sdate = date('Y-m-d');
            $edate = date('Y-m-d', strtotime('+6 months'));
            $pins = $conn->prepare("INSERT INTO enrollment_periods (school_year, semester, start_date, end_date, is_active) VALUES (?, ?, ?, ?, TRUE)");
            $pins->bind_param("ssss", $sy, $sem, $sdate, $edate);
            $pins->execute();
            $period_id = $conn->insert_id;
            $pins->close();
        } else {
            $period_id = $presult->fetch_assoc()['period_id'];
        }
        $pstmt->close();

        // single section entry
        $sec_code = $sec_name;
        $ins = $conn->prepare("INSERT INTO sections (section_code, subject_id, period_id) VALUES (?, ?, ?)");
        $ins->bind_param("sii", $sec_code, $subject_id, $period_id);
        $ins->execute();
        $created = ($ins->affected_rows > 0) ? 1 : 0;
        $ins->close();
        if ($created > 0) {
            echo json_encode(['success' => true, 'message' => 'Section(s) saved!']);
        } else {
            throw new Exception('Insert failed: ' . $conn->error);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
