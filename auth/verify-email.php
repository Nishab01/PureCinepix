<?php

$pagename = "PureCinepix | Verify Email";

require_once '../config/config.php';
require_once '../config/constants.php';

$message = '';
$success = false;

$userId = $_SESSION['pending_verification_user_id'] ?? 0;

if (!$userId) {
    header("Location: register.php");
    exit;
}

if (isset($_POST['verify'])) {

    $otp = trim($_POST['otp']);

    if (!$otp) {

        $message = "Please enter the verification code.";

    } else {

        $stmt = $conn->prepare("
            SELECT *
            FROM email_verifications
            WHERE user_id = ?
            AND otp_code = ?
            AND verified = 0
            LIMIT 1
        ");

        $stmt->bind_param(
            "is",
            $userId,
            $otp
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {

            $message = "Invalid verification code.";

        } else {

            $verification = $result->fetch_assoc();

            if (strtotime($verification['expires_at']) < time()) {

                $message = "Verification code expired.";

            } else {

                $update = $conn->prepare("
                    UPDATE users
                    SET email_verified = 1
                    WHERE id = ?
                ");

                $update->bind_param(
                    "i",
                    $userId
                );

                $update->execute();

                $delete = $conn->prepare("
                    DELETE FROM email_verifications
                    WHERE user_id = ?
                ");

                $delete->bind_param(
                    "i",
                    $userId
                );

                $delete->execute();

                // unset($_SESSION['pending_verification_user_id']);

                // $success = true;

                $result = $conn->query("
                    SELECT *
                    FROM users
                    WHERE id = {$userId}
                    LIMIT 1
                ");

                $user = $result->fetch_assoc();

                session_regenerate_id(true);

                $_SESSION[SESSION_USER] = $user;

                unset($_SESSION['pending_verification_user_id']);

                header("Location: ../pages/");
                exit;
            }
        }
    }
}

include '../includes/header.php';

// if (isset($_SESSION['otp_resent'])) {
//     $message = "A new verification code has been sent.";
//     unset($_SESSION['otp_resent']);
// }

if (!$success && isset($_SESSION['otp_resent'])) {
    $message = "A new verification code has been sent.";
    unset($_SESSION['otp_resent']);
}

?>

<div class="flex items-center justify-center min-h-[80vh] px-4">

    <div class="w-full max-w-md bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-8">

        <h2 class="text-3xl font-bold text-center mb-3">
            Verify Email
        </h2>

        <p class="text-center text-white/60 mb-6">
            Enter the 6-digit code sent to your email.
        </p>

        <?php if ($message): ?>

            <div class="mb-4 text-center text-red-400 text-sm">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

        <?php if ($success): ?>

            <div class="text-center">

                <div class="text-green-400 mb-4">
                    Email verified successfully.
                </div>

                <a
                    href="login.php"
                    class="inline-block px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-500"
                >
                    Login
                </a>

            </div>

        <?php else: ?>

            <form method="POST" class="space-y-4">

                <input
                    type="text"
                    name="otp"
                    maxlength="6"
                    placeholder="Enter OTP"
                    class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none text-center text-lg tracking-[6px]"
                    required
                >

                <button
                    type="submit"
                    name="verify"
                    class="w-full bg-blue-500 hover:bg-blue-600 py-2 rounded-lg font-semibold transition"
                >
                    Verify Email
                </button>

            </form>

            <div class="mt-4 text-center">
                <a
                    href="resend-otp.php"
                    class="text-blue-400 hover:underline text-sm"
                >
                    Resend OTP
                </a>
            </div>

        <?php endif; ?>

    </div>

</div>

<?php include '../includes/footer.php'; ?>