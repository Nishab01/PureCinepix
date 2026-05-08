<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PureCinepix | Sign In</title>
    
    <link rel="icon" type="image/png" sizes="96x96" href="./favicon/favicon-96x96.png">
    <link rel="icon" type="image/svg+xml" href="./favicon/favicon.svg">
    <link rel="shortcut icon" href="./favicon/favicon.ico">
    <link rel="apple-touch-icon" href="./favicon/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-title" content="PureCinepix">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="./favicon/site.webmanifest">
    <meta name="theme-color" content="#00AAFF">
</head>
<body>
    <?php include './app.php'; ?>

    <div class="h-screen flex items-center justify-center">
        <div class="px-4 max-w-screen-xl flex items-stretch justify-center md:flex-row rounded-xl gap-2">
            <div class="p-2 hidden md:block w-1/2 flex justify-center items-center">
                <!-- banner -->
                <img src="./img/signin.webp" alt="" class="object-contain">
            </div>

            <div class="p-2 lg:w-1/2 md:w-1/2 bg-gray-800/50 rounded-xl shadow-lg flex justify-center items-center">
                <form action="./checking.php" method="POST" class="w-full my-2 text-center flex flex-col items-center justify-center">
                    <!-- <p class="text-2xl font-bold mb-2">Create Your Account</p> -->
                    <a href="./signin.php" class="text-2xl font-bold">Welcome</a>
                    <p class="text-sm text-gray-400">Enter your email and password to sign in</p>
                    
                    <div class="my-4 text-left font-semibold w-5/6 lg:w-4/6 md:w-4/6 space-y-4">
                        <div class="space-y-1">
                            <label class="w-full" for="email">Email:</label>
                            <input class="w-full rounded-xl bg-gray-800 border-2 border-blue-500 font-normal" type="email" name="email" id="email" placeholder="Enter your email address" required>
                        </div>

                        <div class="space-y-1">
                            <label class="w-full" for="password">Password:</label>
                            <div class="relative w-full">
                                <input class="w-full rounded-xl bg-gray-800 border-2 border-blue-500 font-normal" type="password" name="password" id="password" placeholder="Enter a password" required>
                                
                                <button type="button" onclick="togglePassword('password', 'eyeIcon')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#00AAFF] transition-colors">
                                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1 text-center">
                            <input class="p-2 transition-all duration-100 w-full cursor-pointer bg-blue-500 hover:bg-blue-700 hover:shadow-lg rounded-xl font-semibold text-base" type="submit" name="signin" id="signin" value="Sign In">
                        </div>
                    </div>

                    <p class="text-sm font-semibold text-gray-200">Don't have an account? <a href="./register.php" class="text-blue-500 underline hover:text-blue-700">Create account</a></p> 
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                // Eye-Slash Icon
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';
            }
            else {
                input.type = 'password';
                // Normal Eye Icon
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
            }
        }
    </script>
</body>
</html>