<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user'])) {
    exit;
}

$user_id = $_SESSION['user']['id'];

$page = max(1, (int)($_GET['page'] ?? 1));
// $limit = 12;
$limit = $paginationLimit;
$offset = ($page - 1) * $limit;

/* FETCH DATA */
$stmt = $conn->prepare("
    SELECT c.*
    FROM watchlist w
    JOIN contents c ON w.content_id = c.id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
    LIMIT ? OFFSET ?
");

$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

/* COUNT */
$stmt2 = $conn->prepare("
    SELECT COUNT(*) as total
    FROM watchlist
    WHERE user_id = ?
");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$total = $stmt2->get_result()->fetch_assoc()['total'];

$totalPages = ceil($total / $limit);

/* OUTPUT HTML */
ob_start();

if ($result->num_rows > 0):

    while ($item = $result->fetch_assoc()):
        $content = $item;
        $forwatchlist = true;
        include '../includes/poster-card.php';
    endwhile;

endif;

$html = ob_get_clean();

echo json_encode([
    'html' => $html,
    'totalPages' => $totalPages
]);