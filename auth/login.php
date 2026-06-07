<?php
$pagename = "PureCinepix | Login";
require_once '../config/config.php';
require_once '../config/constants.php';

$message = "";

// =========================
// HANDLE LOGIN
// =========================
if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email && $password) {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");

        if (!$stmt) {
            $message = "Something went wrong.";
        }
        else {

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {

                $user = $res->fetch_assoc();

                if (password_verify($password, $user['password'])) {

                    // EMAIL NOT VERIFIED
                    if ((int)$user['email_verified'] !== 1) {

                        $_SESSION['pending_verification_user_id'] = $user['id'];

                        $message = '
                            Email not verified yet.
                            <br>
                            <a href="verify-email.php" class="text-blue-400 underline">
                                Verify Email
                            </a>
                        ';

                    } else {

                        // 🔐 REGENERATE SESSION ID
                        session_regenerate_id(true);

                        // STORE SESSION
                        $_SESSION[SESSION_USER] = $user;

                        // REDIRECT
                        header("Location: ./../pages/");
                        exit();
                    }
                }
                else {
                    $message = "Invalid password.";
                }

            }
            else {
                $message = "Email not found.";
            }
        }

    }
    else {
        $message = "All fields are required.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="flex items-center justify-center min-h-[80vh] px-4">

    <div class="w-full max-w-md bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-8">

        <!-- TITLE -->
        <h2 class="text-3xl font-bold text-center mb-6">
            Login
        </h2>

        <!-- MESSAGE -->
        <?php if ($message): ?>
            <div class="mb-4 text-red-400 text-sm text-center">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- FORM -->
        <form method="POST" class="space-y-4">

            <input type="email" name="email" placeholder="Email Address"
                class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none"
                required>

            <input type="password" name="password" placeholder="Password"
                class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 focus:outline-none"
                required>

            <button name="login"
                class="w-full bg-blue-500 hover:bg-blue-600 py-2 rounded-lg font-semibold transition">
                Login
            </button>
        </form>

        <!-- REGISTER LINK -->
        <p class="text-sm text-center mt-4 text-white/70">
            Don’t have an account?
            <a href="../auth/register.php" class="text-blue-400 hover:underline">
                Register
            </a>
        </p>

    </div>

</div>

<?php include '../includes/footer.php'; ?>