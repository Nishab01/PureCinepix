<?php
$pagename = "PureCinepix | Register";
require_once '../config/config.php';
require_once '../config/constants.php';

require_once '../includes/create_email_verification.php';
require_once '../includes/send_otp_email.php';

$message = "";

// =========================
// HANDLE REGISTER
// =========================
if (isset($_POST['register'])) {

    $uname = trim($_POST['uname']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($uname && $email && $password) {

        // CHECK EXISTING USER
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {

            $message = "Email already exists.";

        } else {

            // HASH PASSWORD
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // INSERT USER
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $uname, $email, $hashed);

            if ($stmt->execute()) {

                $userId = (int)$stmt->insert_id;

                $otp = createEmailVerification(
                    $conn,
                    $userId,
                    $email
                );

                $emailSent = sendOtpEmail(
                    $email,
                    $uname,
                    $otp
                );

                if (!$emailSent) {

                    $message = "Failed to send verification email.";

                } else {

                    $_SESSION['pending_verification_user_id'] = $userId;

                    header("Location: verify-email.php");
                    exit;
                }

            }
            else {
                $message = "Something went wrong.";
            }
        }

    } else {
        $message = "All fields are required.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="flex items-center justify-center min-h-[80vh] px-4">

    <div class="w-full max-w-md bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-8">

        <!-- TITLE -->
        <h2 class="text-3xl font-bold text-center mb-6">
            Create Account
        </h2>

        <!-- MESSAGE -->
        <?php if ($message): ?>
            <div class="mb-4 text-red-400 text-sm text-center">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- FORM -->
        <form method="POST" class="space-y-4">

            <input type="text" name="uname" placeholder="Name"
                class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none"
                required>

            <input type="email" name="email" placeholder="Email Address"
                class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none"
                required>

            <input type="password" name="password" placeholder="Password"
                class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none"
                required>

            <button name="register"
                class="w-full bg-blue-500 hover:bg-blue-600 py-2 rounded-lg font-semibold transition">
                Register
            </button>
        </form>

        <!-- LOGIN LINK -->
        <p class="text-sm text-center mt-4 text-white/70">
            Already have an account?
            <a href="./login.php" class="text-blue-400 hover:underline">
                Login
            </a>
        </p>

    </div>

</div>

<?php include '../includes/footer.php'; ?>