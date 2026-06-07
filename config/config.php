<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Localhost Database Connection
$conn = new mysqli("localhost", "root", "", "purecinepix_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$settings = [];
$res = $conn->query("SELECT `key`, `value` FROM settings");
while ($row = $res->fetch_assoc()) {
    $value = is_numeric($row['value']) ? (int)$row['value'] : $row['value'];
    $settings[$row['key']] = $value;
}
$sliderSpeed = (int)($settings['hero_slide_speed'] ?? 5000);
$trailerDelay = (int)($settings['hero_trailer_delay'] ?? 2000);
$paginationLimit = (int)($settings['pagination_limit'] ?? 12);

require_once __DIR__ . '/constants.php';

?>
