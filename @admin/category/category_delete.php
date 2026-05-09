<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleting Category...</title>
    <link rel="icon" type="image/png" sizes="96x96" href="../../favicon/favicon-96x96.png">
    <link rel="icon" type="image/svg+xml" href="../../favicon/favicon.svg">
    <link rel="shortcut icon" href="../../favicon/favicon.ico">
    <link rel="apple-touch-icon" href="../../favicon/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-title" content="PureCinepix">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="../../favicon/site.webmanifest">
    <meta name="theme-color" content="#00AAFF">
</head>
<body>
    <?php
        date_default_timezone_set('Asia/Dhaka');
        $conn = new mysqli('localhost', 'root', '', 'purecinepix');

        $section = "category";
        $slug = trim($_GET['slug']);
        $deletecategory = "DELETE FROM types WHERE `slug` = '".$slug."' AND `section` = '".$section."';";

        $result = mysqli_query($conn, $deletecategory);
        if (!$result) {
            echo "Query Failed: " . mysqli_error($conn);
            ?>
            <script>
                alert("Error Occurred");
                console.log("Query Failed");
                console.log(<?php echo mysqli_error($conn); ?>);
            </script>
            <?php
        }
        else{
            ?>
            <script>
                alert("Category Deleted");
                window.location.href='../contents.php';
            </script>
            <?php
        }
    ?>
</body>
</html>