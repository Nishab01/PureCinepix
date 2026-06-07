<?php

require_once __DIR__ . '/../config/config.php';

function createEmailVerification(
    mysqli $conn,
    int $userId,
    string $email
): string {

    $otp = (string) random_int(100000, 999999);

    $expiresAt = date(
        'Y-m-d H:i:s',
        strtotime('+10 minutes')
    );

    $delete = $conn->prepare("
        DELETE FROM email_verifications
        WHERE user_id = ?
    ");

    $delete->bind_param("i", $userId);
    $delete->execute();

    $insert = $conn->prepare("
        INSERT INTO email_verifications
        (
            user_id,
            email,
            otp_code,
            expires_at
        )
        VALUES
        (
            ?, ?, ?, ?
        )
    ");

    $insert->bind_param(
        "isss",
        $userId,
        $email,
        $otp,
        $expiresAt
    );

    $insert->execute();

    return $otp;
}