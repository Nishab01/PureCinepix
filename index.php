<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/favicon-32.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/favicon-16.ico">
</head>
<body>
    <?php
    require_once './config/config.php';

    // Redirect to actual homepage
    header("Location: ./pages/index.php");
    exit();
    ?>
</body>
</html>

