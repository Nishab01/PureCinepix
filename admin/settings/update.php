<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data) || empty($data)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid or empty data'
    ]);
    exit;
}

try {

    // Prepare once (better performance)
    $checkStmt = $conn->prepare("SELECT id FROM settings WHERE `key`=? LIMIT 1");
    $updateStmt = $conn->prepare("UPDATE settings SET value=? WHERE `key`=?");
    $insertStmt = $conn->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?)");

    foreach ($data as $key => $value) {

        // ✅ Sanitize
        $key = trim($key);

        // Convert arrays to string if needed (future-proof)
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $value = trim((string)$value);

        // ❌ Skip empty keys
        if ($key === '') continue;

        // 🔍 Check existence
        $checkStmt->bind_param("s", $key);
        $checkStmt->execute();
        $res = $checkStmt->get_result();

        if ($res->num_rows > 0) {

            // 🔁 UPDATE
            $updateStmt->bind_param("ss", $value, $key);
            $updateStmt->execute();

        } else {

            // ➕ INSERT
            $insertStmt->bind_param("ss", $key, $value);
            $insertStmt->execute();
        }
    }

    echo json_encode([
        'status' => 'success'
    ]);

} catch (Exception $e) {

    echo json_encode([
        'status' => 'error',
        'message' => 'Server error'
    ]);
}