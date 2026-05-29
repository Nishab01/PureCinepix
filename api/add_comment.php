<?php
session_start();
require_once '../config/config.php';
require_once '../config/constants.php';

header('Content-Type: application/json');

// 🔞 LOAD OFFENSIVE WORDS
$badWords = [];

$res = $conn->query("SELECT `value` FROM settings WHERE `key` = 'offensive_words'");
if ($res && $row = $res->fetch_assoc()) {
    $badWords = array_map('trim', explode(',', $row['value']));
}

// 🔧 PARTIAL CENSOR FUNCTION (s***)
function censorText($text, $badWords) {
    foreach ($badWords as $word) {
        if (!$word) continue;

        $pattern = '/\b' . preg_quote($word, '/') . '\b/i';

        $text = preg_replace_callback($pattern, function($matches) {
            $w = $matches[0];
            return substr($w, 0, 1) . str_repeat('*', strlen($w) - 1);
        }, $text);
    }
    return $text;
}

// 🔐 LOGIN CHECK
if (!isset($_SESSION[SESSION_USER]['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
    exit;
}

$user_id = $_SESSION[SESSION_USER]['id'];

// 📥 INPUT
$content_id = (int)($_POST['content_id'] ?? 0);
$comment1 = trim($_POST['comment'] ?? '');
$comment = censorText($comment1, $badWords);
$parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

// 🚫 VALIDATION
if (!$content_id || !$comment) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// 🚫 LIMIT reply depth (IMPORTANT)
if ($parent_id) {
    $stmt = $conn->prepare("SELECT parent_id FROM comments WHERE id = ?");
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Parent not found']);
        exit;
    }

    $row = $res->fetch_assoc();

    // ❌ prevent replying to a reply
    if ($row['parent_id'] !== null) {
        echo json_encode(['status' => 'error', 'message' => 'Only one level reply allowed']);
        exit;
    }
}

// 💾 INSERT
$stmt = $conn->prepare("
    INSERT INTO comments (content_id, user_id, parent_id, comment)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("iiis", $content_id, $user_id, $parent_id, $comment);

// ✅ EXECUTE FIRST
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
    exit;
}

// ✅ NOW GET ID
$comment_id = $conn->insert_id;

// ✅ FETCH INSERTED COMMENT
$stmt2 = $conn->prepare("
    SELECT c.*, u.name, u.email, u.is_deleted
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.id = ?
");
$stmt2->bind_param("i", $comment_id);
$stmt2->execute();

$res = $stmt2->get_result();
$row = $res->fetch_assoc();

$name = $row['is_deleted']
    ? 'User_' . $row['user_id']
    : $row['name'];

$email = $row['email']
    ? $row['email']
    : '';

// ✅ FINAL RESPONSE
echo json_encode([
    'status' => 'success',
    'comment' => [
        'id' => (int)$row['id'],
        'user_id' => (int)$row['user_id'],
        'name' => htmlspecialchars($name),
        'email' => htmlspecialchars($email),
        'comment' => htmlspecialchars($row['comment']),
        'timestamp' => (int)strtotime($row['created_at']),
        'updated_at' => null,
        'replies' => [],
        'likes' => 0
    ]
]);
exit;