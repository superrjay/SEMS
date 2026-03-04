<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

try {
    // load centralized DB credentials from configuration file
    $cfg = require __DIR__ . '/db_config.php';
    $conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db']);
    if ($conn->connect_error) throw new Exception('DB: ' . $conn->connect_error);
    $conn->set_charset("utf8mb4");

    // GET SECTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getSections') {
        $query = "SELECT s.*, sub.subject_name, sub.subject_code, sub.subject_type,
              r.room_number AS room_number, NULL AS faculty_name
              FROM sections s
              LEFT JOIN subjects sub ON s.subject_id = sub.subject_id
              LEFT JOIN rooms r ON s.room_id = r.room_id
              ORDER BY s.section_id DESC";
        $result = $conn->query($query);
        $sections = [];
        while ($row = $result->fetch_assoc()) {
            $sections[] = $row;
        }

        // If sections reference a faculty_id, resolve a display name safely from employees table
        if (count($sections) > 0) {
            $empCols = [];
            $cres = $conn->query("SHOW COLUMNS FROM employees");
            if ($cres) {
                while ($crow = $cres->fetch_assoc()) $empCols[] = $crow['Field'];
            }
            $pkCandidates = ['faculty_id','employee_id','id','employee_number'];
            $pkCol = null;
            foreach ($pkCandidates as $c) { if (in_array($c, $empCols)) { $pkCol = $c; break; } }

            foreach ($sections as &$s) {
                $s['faculty_name'] = null;
                if (!empty($s['faculty_id']) && $pkCol) {
                    if (in_array('first_name', $empCols) && in_array('last_name', $empCols)) {
                        $nameExpr = "CONCAT_WS(' ', first_name, last_name)";
                    } elseif (in_array('employee_name', $empCols)) {
                        $nameExpr = 'employee_name';
                    } elseif (in_array('first_name', $empCols)) {
                        $nameExpr = 'first_name';
                    } else {
                        $nameExpr = null;
                    }

                    if ($nameExpr) {
                        $sstmt = $conn->prepare("SELECT $nameExpr AS name FROM employees WHERE $pkCol = ? LIMIT 1");
                        if ($sstmt) {
                            $fid = $s['faculty_id'];
                            $sstmt->bind_param('i', $fid);
                            $sstmt->execute();
                            $r = $sstmt->get_result();
                            if ($r && $r->num_rows > 0) {
                                $s['faculty_name'] = $r->fetch_assoc()['name'];
                            }
                            $sstmt->close();
                        }
                    }
                }
            }
            unset($s);
        }
        echo json_encode(['success' => true, 'data' => $sections]);
        exit;
        }

        // GET ALL SCHEDULES (for view_classtimetable.html)
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getScheduleAll') {
            // join with sections so that section_code/year_level are available
            $query = "SELECT sch.*, sec.section_code, sec.year_level
                      FROM schedules sch
                      LEFT JOIN sections sec ON sch.section_id = sec.section_id
                      ORDER BY sec.section_code ASC, sch.schedule_day ASC, sch.time_start ASC";
            $result = $conn->query($query);
            $schedules = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $schedules[] = $row;
                }
            } else {
                // query failed; log for debugging
                error_log('getScheduleAll query failed: ' . $conn->error);
            }
            echo json_encode(['success' => true, 'data' => $schedules]);
            exit;
        }

    // GET SCHEDULE FOR SECTION (used by module2)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getSchedule') {
        $sec_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
        if ($sec_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid section']);
            exit;
        }
        $stmt = $conn->prepare("SELECT * FROM schedules WHERE section_id = ? ORDER BY time_start ASC, schedule_day ASC");
        $stmt->bind_param('i', $sec_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $schedules = [];
        while ($row = $res->fetch_assoc()) {
            $schedules[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $schedules]);
        exit;
    }

    // ASSIGN SCHEDULE (multi-row, schedules table)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'assignSchedule') {
        $sec_id = (int)($_POST['section_id'] ?? 0);
        $day = trim($_POST['schedule_days'] ?? '');
        $stime = trim($_POST['time_start'] ?? '');
        $etime = trim($_POST['time_end'] ?? '');
        $subject = trim($_POST['subject_name'] ?? '');
        $room = trim($_POST['room_number'] ?? '');
        $faculty = trim($_POST['faculty_name'] ?? '');
        if ($sec_id <= 0 || empty($day) || empty($stime) || empty($etime) || empty($subject)) {
            echo json_encode(['success' => false, 'message' => 'Fill all fields']);
            exit;
        }

        // Helper: convert time to minutes
        function toMinutes($t) {
            $parts = explode(':', $t);
            if (count($parts) >= 2) return intval($parts[0])*60 + intval($parts[1]);
            return null;
        }
        $new_start = toMinutes($stime);
        $new_end = toMinutes($etime);

        // Helper: check time overlap
        function rangesOverlap($aStart, $aEnd, $bStart, $bEnd) {
            if ($aStart === null || $bStart === null) return false;
            if ($aEnd === null) $aEnd = $aStart + 60;
            if ($bEnd === null) $bEnd = $bStart + 60;
            return ($aStart < $bEnd) && ($bStart < $aEnd);
        }

        // 1. Room Conflict
        $roomStmt = $conn->prepare("SELECT section_id, subject_name, time_start, time_end, faculty_name FROM schedules WHERE room_number = ? AND schedule_day = ?");
        $roomStmt->bind_param('ss', $room, $day);
        $roomStmt->execute();
        $roomRes = $roomStmt->get_result();
        while ($row = $roomRes->fetch_assoc()) {
            $exist_start = toMinutes($row['time_start']);
            $exist_end = toMinutes($row['time_end']);
            if (rangesOverlap($new_start, $new_end, $exist_start, $exist_end)) {
                echo json_encode(['success' => false, 'message' => 'Room conflict: Room is already booked for another section at this time.']);
                $roomStmt->close();
                exit;
            }
        }
        $roomStmt->close();

        // 2. Instructor Conflict
        if (!empty($faculty)) {
            $facStmt = $conn->prepare("SELECT section_id, room_number, time_start, time_end FROM schedules WHERE faculty_name = ? AND schedule_day = ?");
            $facStmt->bind_param('ss', $faculty, $day);
            $facStmt->execute();
            $facRes = $facStmt->get_result();
            while ($row = $facRes->fetch_assoc()) {
                $exist_start = toMinutes($row['time_start']);
                $exist_end = toMinutes($row['time_end']);
                if (rangesOverlap($new_start, $new_end, $exist_start, $exist_end)) {
                    echo json_encode(['success' => false, 'message' => 'Instructor conflict: Instructor is already assigned to another class at this time.']);
                    $facStmt->close();
                    exit;
                }
            }
            $facStmt->close();
        }

        // 3. Section Conflict
        $secStmt = $conn->prepare("SELECT subject_name, time_start, time_end FROM schedules WHERE section_id = ? AND schedule_day = ?");
        $secStmt->bind_param('is', $sec_id, $day);
        $secStmt->execute();
        $secRes = $secStmt->get_result();
        while ($row = $secRes->fetch_assoc()) {
            $exist_start = toMinutes($row['time_start']);
            $exist_end = toMinutes($row['time_end']);
            if (rangesOverlap($new_start, $new_end, $exist_start, $exist_end)) {
                echo json_encode(['success' => false, 'message' => 'Section conflict: This section already has a scheduled subject at this time.']);
                $secStmt->close();
                exit;
            }
        }
        $secStmt->close();

        // Prevent duplicate slot for same section, day, and time
        $dupStmt = $conn->prepare("SELECT schedule_id FROM schedules WHERE section_id = ? AND schedule_day = ? AND time_start = ? AND time_end = ? LIMIT 1");
        $dupStmt->bind_param('isss', $sec_id, $day, $stime, $etime);
        $dupStmt->execute();
        $dupRes = $dupStmt->get_result();
        if ($dupRes && $dupRes->num_rows > 0) {
            // already exists: treat as success so the frontend doesn't flag an error
            echo json_encode(['success' => true, 'message' => 'Slot already exists']);
            exit;
        }
        $dupStmt->close();
        // Insert schedule row (only columns that exist in table)
        $insStmt = $conn->prepare("INSERT INTO schedules (section_id, subject_name, room_number, faculty_name, schedule_day, time_start, time_end) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insStmt->bind_param('issssss', $sec_id, $subject, $room, $faculty, $day, $stime, $etime);
        $insStmt->execute();
        $insStmt->close();
        echo json_encode(['success' => true, 'message' => 'Schedule assigned!']);
        exit;
    }

    // UPDATE SECTION SCHEDULE (schedule_days and time_start in sections table)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateSectionSchedule') {
        $sec_id = (int)($_POST['section_id'] ?? 0);
        $day = trim($_POST['schedule_days'] ?? '');
        $stime = trim($_POST['time_start'] ?? '');
        $etime = trim($_POST['time_end'] ?? '');
        if ($sec_id <= 0 || empty($day) || empty($stime)) {
            echo json_encode(['success' => false, 'message' => 'Fill all fields for section update']);
            exit;
        }
        // Update the sections table with the new schedule_days and time_start (and optionally time_end)
        if (!empty($etime)) {
            $upd = $conn->prepare("UPDATE sections SET schedule_days = ?, time_start = ?, time_end = ? WHERE section_id = ?");
            $upd->bind_param("sssi", $day, $stime, $etime, $sec_id);
        } else {
            $upd = $conn->prepare("UPDATE sections SET schedule_days = ?, time_start = ? WHERE section_id = ?");
            $upd->bind_param("ssi", $day, $stime, $sec_id);
        }
        $upd->execute();
        $upd->close();
        // Optionally fetch updated section
        $sstmt = $conn->prepare("SELECT * FROM sections WHERE section_id = ? LIMIT 1");
        $sstmt->bind_param("i", $sec_id);
        $sstmt->execute();
        $res = $sstmt->get_result();
        $updated = $res->fetch_assoc();
        $sstmt->close();
        echo json_encode(['success' => true, 'message' => 'Section schedule updated!', 'data' => $updated]);
        exit;
    }
    // ASSIGN ROOM
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'assignRoom') {
        $sec_id = (int)($_POST['section_id'] ?? 0);
        $room_no = trim($_POST['room_number'] ?? '');

        if ($sec_id <= 0 || empty($room_no)) {
            echo json_encode(['success' => false, 'message' => 'Fill all fields']);
            exit;
        }

        // Check/create room
        $stmt = $conn->prepare("SELECT room_id FROM rooms WHERE room_number = ?");
        $stmt->bind_param("s", $room_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $build = "Building A";
            $cap = 40;
            $ins = $conn->prepare("INSERT INTO rooms (room_number, building, capacity) VALUES (?, ?, ?)");
            $ins->bind_param("ssi", $room_no, $build, $cap);
            $ins->execute();
            $room_id = $conn->insert_id;
            $ins->close();
        } else {
            $room_id = $result->fetch_assoc()['room_id'];
        }
        $stmt->close();

        // Assign room to section
        $upd = $conn->prepare("UPDATE sections SET room_id = ? WHERE section_id = ?");
        $upd->bind_param("ii", $room_id, $sec_id);
        $upd->execute();
        // Fetch updated section to return to client
        $sstmt = $conn->prepare("SELECT s.*, sub.subject_name, sub.subject_code, r.room_number AS room_number, NULL AS faculty_name
                     FROM sections s
                     LEFT JOIN subjects sub ON s.subject_id = sub.subject_id
                     LEFT JOIN rooms r ON s.room_id = r.room_id
                     WHERE s.section_id = ? LIMIT 1");
        $sstmt->bind_param("i", $sec_id);
        $sstmt->execute();
        $sres = $sstmt->get_result();
        $updated = $sres->fetch_assoc();
        $sstmt->close();

        echo json_encode(['success' => true, 'message' => 'Room assigned!', 'data' => $updated]);
        exit;
    }

    // ASSIGN TEACHER
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'assignTeacher') {
        $sec_id = (int)($_POST['section_id'] ?? 0);
        $teacher = trim($_POST['faculty_name'] ?? '');

        if ($sec_id <= 0 || empty($teacher)) {
            echo json_encode(['success' => false, 'message' => 'Fill all fields']);
            exit;
        }

        // Discover employees table columns so we can search safely across different schemas
        $cols = [];
        $cres = $conn->query("SHOW COLUMNS FROM employees");
        while ($crow = $cres->fetch_assoc()) $cols[] = $crow['Field'];

        // Determine PK column in employees table (common names)
        $pkCandidates = ['faculty_id','employee_id','id','employee_number'];
        $pkCol = null;
        foreach ($pkCandidates as $c) { if (in_array($c, $cols)) { $pkCol = $c; break; } }

        // Build search query depending on available columns
        $pattern = "%$teacher%";
        if (in_array('first_name', $cols) && in_array('last_name', $cols) && $pkCol) {
            $q = "SELECT $pkCol AS pid, CONCAT_WS(' ', first_name, last_name) AS fullname FROM employees WHERE CONCAT_WS(' ', first_name, last_name) LIKE ? LIMIT 1";
        } elseif (in_array('employee_name', $cols) && $pkCol) {
            $q = "SELECT $pkCol AS pid, employee_name AS fullname FROM employees WHERE employee_name LIKE ? LIMIT 1";
        } elseif (in_array('first_name', $cols) && $pkCol) {
            $q = "SELECT $pkCol AS pid, first_name AS fullname FROM employees WHERE first_name LIKE ? LIMIT 1";
        } else {
            // Fallback: try to find any matching column by name using LIKE on several possible fields
            $likeCols = array_intersect(['first_name','last_name','employee_name','employee_number'], $cols);
            if (count($likeCols) > 0 && $pkCol) {
                $conds = [];
                foreach ($likeCols as $lc) $conds[] = "$lc LIKE ?";
                $q = "SELECT $pkCol AS pid, " . (in_array('first_name',$cols) ? "CONCAT_WS(' ', first_name, last_name)" : (in_array('employee_name',$cols)?'employee_name':'NULL')) . " AS fullname FROM employees WHERE (" . implode(' OR ', $conds) . ") LIMIT 1";
            } else {
                throw new Exception('Employees table schema not compatible');
            }
        }

        $stmt = $conn->prepare($q);
        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);

        // Bind parameters depending on number of LIKE columns
        if (isset($likeCols) && count($likeCols) > 0 && strpos($q, ' OR ') !== false) {
            // bind same pattern for each ?
            $types = str_repeat('s', count($likeCols));
            $params = array_fill(0, count($likeCols), $pattern);
            $stmt->bind_param($types, ...$params);
        } else {
            $stmt->bind_param('s', $pattern);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows == 0) {
            // Teacher not found - create a new employees row using available columns
            $stmt->close();
            // helper: generate a collision-resistant employee_number when needed
            $hasEmpNum = in_array('employee_number', $cols);
            $generateEmpNum = function() use ($conn) {
                do {
                    $emp = uniqid('EMP');
                    $cst = $conn->prepare("SELECT 1 FROM employees WHERE employee_number = ? LIMIT 1");
                    $cst->bind_param('s', $emp);
                    $cst->execute();
                    $cres2 = $cst->get_result();
                    $exists = ($cres2 && $cres2->num_rows > 0);
                    $cst->close();
                } while ($exists);
                return $emp;
            };

            // Determine how to insert based on available columns
            if (in_array('first_name', $cols) && in_array('last_name', $cols)) {
                $parts = preg_split('/\s+/', $teacher, 2);
                $fn = $parts[0] ?? $teacher;
                $ln = $parts[1] ?? '';
                if ($hasEmpNum) {
                    $empnum = $generateEmpNum();
                    $ins = $conn->prepare("INSERT INTO employees (employee_number, first_name, last_name, employee_type, employment_status) VALUES (?, ?, ?, 'Faculty', 'Regular')");
                    $ins->bind_param("sss", $empnum, $fn, $ln);
                } else {
                    $ins = $conn->prepare("INSERT INTO employees (first_name, last_name, employee_type, employment_status) VALUES (?, ?, 'Faculty', 'Regular')");
                    $ins->bind_param("ss", $fn, $ln);
                }
                $ins->execute();
                $faculty_id = $conn->insert_id;
                $fullname = trim($fn . ' ' . $ln);
                $ins->close();
            } elseif (in_array('employee_name', $cols)) {
                if ($hasEmpNum) {
                    $empnum = $generateEmpNum();
                    $ins = $conn->prepare("INSERT INTO employees (employee_number, employee_name, employee_type, employment_status) VALUES (?, ?, 'Faculty', 'Regular')");
                    $ins->bind_param("ss", $empnum, $teacher);
                } else {
                    $ins = $conn->prepare("INSERT INTO employees (employee_name, employee_type, employment_status) VALUES (?, 'Faculty', 'Regular')");
                    $ins->bind_param("s", $teacher);
                }
                $ins->execute();
                $faculty_id = $conn->insert_id;
                $fullname = $teacher;
                $ins->close();
            } elseif (in_array('employee_number', $cols)) {
                // minimal insert using employee_number and a generated name
                $empnum = $generateEmpNum();
                $ins = $conn->prepare("INSERT INTO employees (employee_number, first_name, last_name, employee_type, employment_status) VALUES (?, ?, ?, 'Faculty', 'Regular')");
                $parts = preg_split('/\s+/', $teacher, 2);
                $fn = $parts[0] ?? $teacher;
                $ln = $parts[1] ?? '';
                $ins->bind_param("sss", $empnum, $fn, $ln);
                $ins->execute();
                $faculty_id = $conn->insert_id;
                $fullname = trim($fn . ' ' . $ln);
                $ins->close();
            } else {
                // Last resort: cannot create; return error
                echo json_encode(['success' => false, 'message' => 'Employees schema incompatible - cannot create teacher']);
                exit;
            }
        } else {
            $found = $res->fetch_assoc();
            $faculty_id = $found['pid'];
            $fullname = $found['fullname'] ?? $teacher;
            $stmt->close();
        }

        // Assign teacher to section (store PK value)
        $upd = $conn->prepare("UPDATE sections SET faculty_id = ? WHERE section_id = ?");
        if (!$upd) throw new Exception('Prepare failed: ' . $conn->error);
        $upd->bind_param("si", $faculty_id, $sec_id);
        $upd->execute();

        // Return updated section info (without fragile joins)
        $sstmt = $conn->prepare("SELECT s.*, sub.subject_name, sub.subject_code, r.room_number AS room_number
                                 FROM sections s
                                 LEFT JOIN subjects sub ON s.subject_id = sub.subject_id
                                 LEFT JOIN rooms r ON s.room_id = r.room_id
                                 WHERE s.section_id = ? LIMIT 1");
        $sstmt->bind_param("i", $sec_id);
        $sstmt->execute();
        $sres = $sstmt->get_result();
        $updated = $sres->fetch_assoc();
        $sstmt->close();

        // attach faculty display name we resolved
        $updated['faculty_name'] = $fullname;

        echo json_encode(['success' => true, 'message' => 'Teacher assigned!', 'data' => $updated]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
