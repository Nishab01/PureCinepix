<?php
session_start();
require_once '../config/config.php';
require_once '../config/constants.php';

header('Content-Type: application/json');

$user_id = $_SESSION[SESSION_USER]['id'] ?? 0;

// 📥 INPUT
$content_id = (int)($_GET['content_id'] ?? 0);
$limit = 5;
$offset = (int)($_GET['offset'] ?? 0);

if (!$content_id) {
    echo json_encode(['comments' => []]);
    exit;
}

# ==========================
# 1️⃣ FETCH MAIN COMMENTS
$stmt = $conn->prepare("
    SELECT c.*, u.name, u.email, u.is_deleted
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.content_id = ? AND c.parent_id IS NULL
    ORDER BY c.id DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $content_id, $limit, $offset);
$stmt->execute();
$res = $stmt->get_result();

$comments = [];
$commentIds = [];

while ($row = $res->fetch_assoc()) {

    $name = $row['is_deleted']
        ? 'User_' . $row['user_id']
        : $row['name'];
    $email = $row['email']
        ? $row['email']
        : '';

    $comments[$row['id']] = [
        'id' => (int)$row['id'],
        'user_id' => (int)$row['user_id'],
        'name' => htmlspecialchars($name),
        'email' => htmlspecialchars($email),
        'comment' => htmlspecialchars($row['comment']),
        'timestamp' => (int)strtotime($row['created_at']),
        'updated_at' => $row['updated_at'] ? (int)strtotime($row['updated_at']) : null,
        'replies' => [],
        'likes' => 0,
        'liked' => false
    ];

    $commentIds[] = $row['id'];
}

# ==========================
# 2️⃣ FETCH REPLIES
// if (!empty($allIds)) {
//     $ids = implode(',', $allIds);

//     $replyRes = $conn->query("
//         SELECT c.*, u.name, u.is_deleted
//         FROM comments c
//         LEFT JOIN users u ON c.user_id = u.id
//         WHERE c.parent_id IN ($ids)
//         ORDER BY c.id ASC
//     ");

//     $allIds = $commentIds;

//     while ($row = $replyRes->fetch_assoc()) {

//         $name = $row['is_deleted']
//             ? 'User_' . $row['user_id']
//             : $row['name'];

//         $replyId = (int)$row['id'];

//         $reply = [
//             'id' => $replyId,
//             'user_id' => (int)$row['user_id'],
//             'name' => htmlspecialchars($name),
//             'comment' => htmlspecialchars($row['comment']),
//             'timestamp' => (int)strtotime($row['created_at']),
//             'updated_at' => $row['updated_at'] ? (int)strtotime($row['updated_at']) : null,
//             'likes' => 0,
//             'liked' => false
//         ];

//         $comments[$row['parent_id']]['replies'][] = $reply;

//         $allIds[] = $replyId;
//     }
// }
$allIds = $commentIds; // ✅ DEFINE FIRST

if (!empty($commentIds)) {
    $ids = implode(',', $commentIds);

    $replyRes = $conn->query("
        SELECT c.*, u.name, u.is_deleted
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE c.parent_id IN ($ids)
        ORDER BY c.id ASC
    ");

    while ($row = $replyRes->fetch_assoc()) {
        $name = $row['is_deleted']
            ? 'User_' . $row['user_id']
            : $row['name'];

        $replyId = (int)$row['id'];

        $reply = [
            'id' => $replyId,
            'user_id' => (int)$row['user_id'],
            'name' => htmlspecialchars($name),
            'comment' => htmlspecialchars($row['comment']),
            'timestamp' => (int)strtotime($row['created_at']),
            'updated_at' => $row['updated_at'] ? (int)strtotime($row['updated_at']) : null,
            'likes' => 0,
            'liked' => false
        ];

        $comments[$row['parent_id']]['replies'][] = $reply;

        $allIds[] = $replyId; // ✅ collect reply IDs
    }
}

# ==========================
# 3️⃣ FETCH LIKE COUNTS
if (!empty($allIds)) {
    $ids = implode(',', $allIds);

    $likeRes = $conn->query("
        SELECT comment_id, COUNT(*) as total
        FROM comment_likes
        WHERE comment_id IN ($ids)
        GROUP BY comment_id
    ");

    // while ($row = $likeRes->fetch_assoc()) {
    //     $comments[$row['comment_id']]['likes'] = (int)$row['total'];
    // }

    while ($row = $likeRes->fetch_assoc()) {
        $cid = (int)$row['comment_id'];
        $count = (int)$row['total'];

        if (isset($comments[$cid])) {
            $comments[$cid]['likes'] = $count;
        } else {
            // ✅ Otherwise, find inside replies
            foreach ($comments as &$comment) {
                foreach ($comment['replies'] as &$reply) {
                    if ($reply['id'] === $cid) {
                        $reply['likes'] = $count;
                        break 2;
                    }
                }
            }
        }
    }
}

# ==========================
# 4️⃣ CHECK USER LIKES
if ($user_id && !empty($allIds)) {
    $ids = implode(',', $allIds);

    $likeRes = $conn->query("
        SELECT comment_id
        FROM comment_likes
        WHERE comment_id IN ($ids) AND user_id = $user_id
    ");

    while ($row = $likeRes->fetch_assoc()) {
        // $comments[$row['comment_id']]['liked'] = true;

        $cid = (int)$row['comment_id'];

        if (isset($comments[$cid])) {
            $comments[$cid]['liked'] = true;
        } else {
            foreach ($comments as &$comment) {
                foreach ($comment['replies'] as &$reply) {
                    if ($reply['id'] === $cid) {
                        $reply['liked'] = true;
                        break 2;
                    }
                }
            }
        }
    }
}

# ==========================
# ✅ FINAL OUTPUT
echo json_encode([
    'comments' => array_values($comments)
]);
