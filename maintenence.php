<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<link rel="icon" type="image/png" sizes="96x96" href="./favicon/favicon-96x96.png">
<link rel="icon" type="image/svg+xml" href="./favicon/favicon.svg">
<link rel="shortcut icon" href="./favicon/favicon.ico">
<link rel="apple-touch-icon" href="./favicon/apple-touch-icon.png">
<meta name="apple-mobile-web-app-title" content="PureCinepix">
<meta name="mobile-web-app-capable" content="yes">
<link rel="manifest" href="./favicon/site.webmanifest">
<meta name="theme-color" content="#00AAFF">

<title>Maintenance in progress</title>

<style>
/* Reset */
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: Arial, sans-serif;
    overflow: hidden;
}

/* Animated background */
body {
    background: linear-gradient(-45deg, #0f172a, #1e293b, #0271a5, #5c5db3);
    background-size: 400% 400%;
    animation: gradientMove 12s ease infinite;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
}

@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Floating bubbles */
.bubble {
    position: absolute;
    bottom: -150px;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
    animation: rise 20s infinite ease-in;
}

@keyframes rise {
    0% { transform: translateY(0) scale(1); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateY(-110vh) scale(1.5); opacity: 0; }
}

/* Center layout */
.container {
    text-align: center;
    z-index: 10;
}

/* Bouncing image */
.logo {
    width: 250px;
    height: auto;
    margin-bottom: 10px;
    animation: bounce 2.5s infinite;
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.4));
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-18px); }
}

/* Fancy title */
h1 {
    font-size: 3rem;
    margin: 0;
    letter-spacing: 2px;
    background: linear-gradient(90deg, #ffffff, #7dd3fc, #c084fc);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: glowText 3s ease-in-out infinite;
}

@keyframes glowText {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Subtitle */
p {
    margin-top: 10px;
    font-size: 1.2rem;
    opacity: 0.85;
    letter-spacing: 1px;
}

/* Loader */
.loader {
    margin: 25px auto 0;
    width: 40px;
    height: 40px;
    border: 4px solid rgba(255,255,255,0.25);
    border-top: 4px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}
</style>
</head>

<body>


<?php
    date_default_timezone_set('Asia/Dhaka');
    $conn = new mysqli('localhost', 'root', '', 'purecinepix');

    $maintenance = "SELECT * FROM settings WHERE `key` = 'maintenance-mode';";
    $runq = $conn->query($maintenance);
    $mode = $runq->fetch_assoc();
    if($mode['value'] == 0){
        ?>
        <script>
            alert("The website in on live.");
            window.location.href='./';
        </script>
        <?php
    }
?>

<!-- bubbles -->
<?php for ($i = 0; $i < 20; $i++): ?>
<div class="bubble" style="
    left: <?= rand(0, 100) ?>%;
    width: <?= rand(10, 60) ?>px;
    height: <?= rand(10, 60) ?>px;
    animation-duration: <?= rand(10, 25) ?>s;
    animation-delay: <?= rand(0, 10) ?>s;
"></div>
<?php endfor; ?>

<div class="container">

    <img src="./favicon/web-app-manifest-512x512.png" class="logo" alt="logo">

    <h1>System Under Maintenance</h1>

    <p>
        We’re upgrading things behind the scenes.<br>
        Everything will be back online shortly.
    </p>

    <div class="loader"></div>

</div>

<script>

setTimeout(() => {
    location.reload();
}, 300000); // 5 minutes
</script>


</body>
</html>