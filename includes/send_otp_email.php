<?php

require_once __DIR__ . '/../config/mail.php';

function sendOtpEmail($email, $name, $otp)
{
    $payload = [
        'sender' => [
            'name'  => MAIL_FROM_NAME,
            'email' => MAIL_FROM
        ],
        'to' => [
            [
                'email' => $email,
                'name'  => $name
            ]
        ],
        'subject' => 'Verify Your Email - PureCinepix',
        'htmlContent' => "
            <h2>Welcome to PureCinepix</h2>

            <p>Hello {$name},</p>

            <p>Your verification code is:</p>

            <h1 style='letter-spacing:5px'>{$otp}</h1>

            <p>This code will expire in 10 minutes.</p>

            <p>If you did not request this code, please ignore this email.</p>
        "
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.brevo.com/v3/smtp/email',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'api-key: ' . BREVO_API_KEY,
            'content-type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return $httpCode === 201;
}