<?php
    require_once '../config/config.php';

    $id = (int)($_GET['download'] ?? 0);
    $go = (int)($_GET['go'] ?? 0);

    $stmt = $conn->prepare("
        SELECT
            d.url,
            d.quality,
            c.title,
            c.poster1,
            c.poster4,
            c.backdrop1,
            c.backdrop4
        FROM downloads d
        LEFT JOIN contents c ON c.id = d.content_id
        WHERE d.id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Invalid download link.");
    }

    $data = $result->fetch_assoc();

    $url      = $data['url'];
    $quality  = htmlspecialchars($data['quality']);
    $title    = htmlspecialchars($data['title']);
    $poster1   = $data['poster1'];
    $poster   = $data['poster4'];
    $backdrop1   = $data['backdrop1'];
    $backdrop = $data['backdrop4'];

    if ($go === 1) {
        header("Location: " . $url);
        exit;
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" sizes="32x32" href="../assets/favicon-32.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/favicon-16.ico">

    <script src="../assets/js/tw.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title><?= $title ?> - Download</title>

    <script>
    let seconds = 5;

    function updateCountdown() {

        document.getElementById('countdown').innerText = seconds;

        if(seconds <= 0){
            window.location.href =
                "download.php?download=<?= $id ?>&go=1";
            return;
        }

        seconds--;

        setTimeout(updateCountdown, 1000);
    }

    window.onload = updateCountdown;
</script>

<style>

    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
    }

    body{
        font-family:Inter,Arial,sans-serif;
        min-height:100vh;
        overflow:hidden;
        color:white;
    }

    /* BACKGROUND */

    .bg{
        position:fixed;
        inset:0;
        z-index:-2;
    }

    .bg img{
        width:100%;
        height:100%;
        object-fit:cover;
        filter:blur(12px);
        transform:scale(1.1);
    }

    /* DESKTOP BACKDROP */

    .desktop-bg{
        display:block;
    }

    .mobile-bg{
        display:none;
    }

    @media(max-width:768px){

        .desktop-bg{
            display:none;
        }

        .mobile-bg{
            display:block;
        }
    }

    /* OVERLAY */

    .overlay{
        position:fixed;
        inset:0;
        background:
            linear-gradient(
                to bottom,
                rgba(0,0,0,.55),
                rgba(0,0,0,.85)
            );
        z-index:-1;
    }

    /* CENTER */

    .wrapper{
        min-height:100vh;
        display:flex;
        justify-content:center;
        align-items:center;
        padding:20px;
    }

    /* GLASS CARD */

    .card{
        width:100%;
        max-width:650px;

        background:rgba(255,255,255,.08);

        backdrop-filter:blur(20px);
        -webkit-backdrop-filter:blur(20px);

        padding:40px;

        text-align:center;

        box-shadow:
            0 20px 50px rgba(0,0,0,.45);
    }

    .logo img{
        height:220px;
        height:auto;
        margin-bottom:20px;
        border-radius:10px;
        border: 2px solid black;
    }

    .quality{
        display:inline-block;

        padding:8px 16px;

        border-radius:999px;

        background:rgba(59,130,246,.15);

        border:1px solid rgba(59,130,246,.35);

        color:#93c5fd;

        font-size:14px;

        margin-bottom:25px;
    }

    .timer{
        margin-top:10px;
        margin-bottom:10px;
        font-size:50px;
        font-weight:800;
        color:#3b82f6;
        line-height:1;
    }

    .note{
        margin-top:20px;
        color:rgba(255,255,255,.7);
        font-size:15px;
    }

    .small{
        margin-top:10px;
        color:rgba(255,255,255,.45);
        font-size:13px;
    }

    @media(max-width:768px){

        .card{
            padding:30px 20px;
        }

        .title{
            font-size:22px;
        }

        .timer{
            font-size:50px;
        }
    }

</style>
</head>
<body>

    <!-- DESKTOP BACKDROP -->
    <div class="bg desktop-bg">
        <img src="<?= $backdrop ?>">
    </div>

    <!-- MOBILE POSTER -->
    <div class="bg mobile-bg">
        <img src="<?= $poster ?>">
    </div>

    <div class="overlay"></div>

    <div class="wrapper">

        <div class="card shadow-lg border border-black rounded-lg">

            <div class="hidden md:flex items-center justify-center">
                <img class="w-full rounded-lg shadow-lg" src="<?= $backdrop1 ?>" alt="">
            </div>

            <div class="flex md:hidden items-center justify-center">
                <img class="h-[40vh] rounded-lg shadow-lg" src="<?= $poster1 ?>" alt="">
            </div>

            <br>

            <div class="title">
                <p><?= $title ?></p>
            </div>

            <br>

            <div class="small">
                Preparing Your Download
            </div>

            <div id="countdown" class="timer">
                5
            </div>

            <div class="small">
                Your download will start automatically.
            </div>

        </div>

    </div>

</body>
</html>