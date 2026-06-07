<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="cw18mpWd3hibuLyUpAIszw1BHE9wbtoEDqHuPbmg23Q" />
    <title>PureCinepix</title>

    <link rel="icon" href="./assets/favicon-32.ico">
    <link rel="icon" href="./assets/favicon-16.ico">

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
    <?php
        // require_once './config/config.php';
        // require_once './config/constants.php';

        // Redirect to actual homepage
        header("Location: ./pages/");
        exit();
    ?>
</body>
</html>