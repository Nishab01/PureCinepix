<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PureCinepix</title>

    <link rel="icon" href="./assets/favicon-32.ico">

    <script src="./assets/js/tw.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .loader {
            border: 4px solid rgba(255,255,255,0.15);
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-[#0b0f19] text-white min-h-screen flex items-center justify-center">

    <div class="text-center px-6">

        <img src="./assets/logo.svg"
             alt="PureCinepix"
             class="h-32 mx-auto mb-6">

        <div class="loader mx-auto mb-6"></div>

        <h1 class="text-3xl font-bold mb-3">
            Welcome to PureCinepix
        </h1>

        <p class="text-gray-400 mb-4">
            Your entertainment destination is loading...
        </p>

        <p class="text-lg">
            Redirecting in
            <span id="countdown" class="font-bold text-blue-400">1</span>
            seconds
        </p>

        <div class="mt-6">
            <a href="https://purecinepix.site.je/pages/"
               class="px-5 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition font-bold">
                Go Now
            </a>
        </div>

    </div>

    <script>
        let seconds = 1;

        const timer = setInterval(() => {
            seconds--;

            document.getElementById('countdown').textContent = seconds;

            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = "https://purecinepix.site.je/pages/";
            }
        }, 1000);
    </script>

</body>
</html>