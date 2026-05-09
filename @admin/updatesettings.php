<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saving...</title>
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

        $trending_count = trim($_POST['trending_count']);
        $featured_count = trim($_POST['featured_count']);
        $latest_count = trim($_POST['latest_count']);
        $genrewise_count = trim($_POST['genrewise_count']);

        $website_email = trim($_POST['website_email']);
        $website_about = trim($_POST['website_about']);

        $facebook_url = trim($_POST['facebook_url']);
        $facebook_icon = trim($_POST['facebook_icon']);
        $instagram_url = trim($_POST['instagram_url']);
        $instagram_icon = trim($_POST['instagram_icon']);

        $copyright_text = trim($_POST['copyright_text']);
        $footer_text = trim($_POST['footer_text']);
        $footer_description = trim($_POST['footer_description']);

        $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;

        $update1 = "UPDATE settings SET `value` = '".$trending_count."' WHERE `key` = 'trending-count';";
        $update2 = "UPDATE settings SET `value` = '".$featured_count."' WHERE `key` = 'featured-count';";
        $update3 = "UPDATE settings SET `value` = '".$latest_count."' WHERE `key` = 'latest-count';";
        $update4 = "UPDATE settings SET `value` = '".$genrewise_count."' WHERE `key` = 'genrewise-count';";

        $update5 = "UPDATE settings SET `value` = '".$website_email."' WHERE `key` = 'website-email';";
        $update6 = "UPDATE settings SET `value` = '".$website_about."' WHERE `key` = 'website-about';";

        $update7 = "UPDATE settings SET `value` = '".$facebook_url."' WHERE `key` = 'facebook';";
        $update8 = "UPDATE settings SET `icon` = '".$facebook_icon."' WHERE `key` = 'facebook';";
        $update9 = "UPDATE settings SET `value` = '".$instagram_url."' WHERE `key` = 'instagram';";
        $update10 = "UPDATE settings SET `icon` = '".$instagram_icon."' WHERE `key` = 'instagram';";

        $update11 = "UPDATE settings SET `value` = '".$copyright_text."' WHERE `key` = 'copyright-text';";
        $update12 = "UPDATE settings SET `value` = '".$footer_text."' WHERE `key` = 'footer-text';";
        $update13 = "UPDATE settings SET `value` = '".$footer_description."' WHERE `key` = 'footer-description';";

        $update14 = "UPDATE settings SET `value` = '".$maintenance_mode."' WHERE `key` = 'maintenance-mode';";

        $queryarray = [$update1, $update2, $update3, $update4, $update5, $update6, $update7, $update8, $update9, $update10, $update11, $update12, $update13, $update14];

        foreach ($queryarray as $query) {
            if (!empty($query)) {
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo "Query Failed: " . mysqli_error($conn);
                    ?>
                    <script>
                        alert("Update Unsuccessful");
                        console.log("Query Failed");
                        console.log(<?php echo mysqli_error($conn); ?>);
                    </script>
                    <?php
                }
                else{
                    ?>
                    <script>
                        alert("Successfully Updated");
                        window.location.href='../settings.php';
                    </script>
                    <?php
                }
            }
        }
    ?>
</body>
</html>