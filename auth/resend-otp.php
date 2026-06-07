<?php

require_once '../config/config.php';
require_once '../config/constants.php';

require_once '../includes/create_email_verification.php';
require_once '../includes/send_otp_email.php';

$userId = $_SESSION['pending_verification_user_id'] ?? 0;

if (!$userId) {
    header("Location: register.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Get User
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id, name, email
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param(
    "i",
    $userId
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: register.php");
    exit;
}

$user = $result->fetch_assoc();

/*
|--------------------------------------------------------------------------
| Generate New OTP
|--------------------------------------------------------------------------
*/

$otp = createEmailVerification(
    $conn,
    (int)$user['id'],
    $user['email']
);

/*
|--------------------------------------------------------------------------
| Send Email
|--------------------------------------------------------------------------
*/

sendOtpEmail(
    $user['email'],
    $user['name'],
    $otp
);

/*
|--------------------------------------------------------------------------
| Back To Verify Page
|--------------------------------------------------------------------------
*/

$_SESSION['otp_resent'] = true;

header("Location: verify-email.php");
exit;