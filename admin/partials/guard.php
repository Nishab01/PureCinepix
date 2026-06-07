<?php


include '../partials/favicon.php';

// START SESSION SAFELY
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// LOAD DB
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/constants.php';


// ❌ NOT LOGGED IN
if (!isset($_SESSION[SESSION_USER]['id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// GET USER ID
$user_id = $_SESSION[SESSION_USER]['id'];

// ✅ VERIFY FROM DATABASE (REAL-TIME)
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// ❌ USER NOT FOUND
if ($result->num_rows === 0) {
    session_destroy();
    header("Location: ../../auth/login.php");
    exit;
}

$user = $result->fetch_assoc();

// ❌ NOT ADMIN
if($user['role'] !== 'admin'){
    if($user['role'] !== 'superadmin'){
        header("Location: ../../auth/login.php");
        exit;
    }
}

// ✅ OPTIONAL: STORE ROLE (for later UI use)
$_SESSION[SESSION_USER]['role'] = $user['role'];