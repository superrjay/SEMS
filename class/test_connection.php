<?php
// Ultra simple test
$conn = mysqli_connect("127.0.0.1", "root", "", "enrollment_system");

echo "<h1>System Status</h1>";

if (mysqli_connect_errno()) {
    echo "<p style='color:red;'><b>❌ FAILED:</b> " . mysqli_connect_error() . "</p>";
    echo "<p>MySQL is NOT running. Start XAMPP MySQL immediately.</p>";
    exit;
}

echo "<p style='color:green;'><b>✅ Database Connected</b></p>";

// Check tables
$tables = mysqli_query($conn, "SHOW TABLES");
$table_list = [];
while($row = mysqli_fetch_array($tables)) {
    $table_list[] = $row[0];
}

echo "<p>Found " . count($table_list) . " tables</p>";

// Check if we can insert (test write)
$test_run = @mysqli_query($conn, "SELECT 1 FROM sections LIMIT 1");
if ($test_run === false) {
    echo "<p style='color:red;'><b>❌ Sections table not found!</b></p>";
} else {
    echo "<p style='color:green;'><b>✅ Tables accessible</b></p>";
}

// Try to get sections
$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM sections");
$row = mysqli_fetch_array($result);
echo "<p><b>Sections in database:</b> " . $row['cnt'] . "</p>";

echo "<hr>";
echo "<a href='module1.html'>Go to Module 1</a>";

mysqli_close($conn);
?>
