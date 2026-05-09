<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Updating Category...</title>
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

        function formatName($text) {
            $parts = explode(',', $text);
            $formatted = array_map(function($item) {
                $words = explode(' ', trim($item));
                $words = array_map(function($word) {
                    if (strtoupper($word) === $word) {
                        return $word;
                    }
                    if (preg_match('/[A-Z].*[A-Z]/', $word)) {
                        return $word;
                    }
                    return ucfirst(strtolower($word));
                }, $words);
                return implode(' ', $words);
            }, $parts);
            return implode(', ', $formatted);
        }


        $dataid = trim($_POST['dataid']);
        $section = "category";
        $name = formatName(trim($_POST['name']));

        $string = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        $string = trim($string, '-');
        $slug = $string.'-'.rand(10, 99);

        $banner = trim($_POST['banner']);

        $status = trim($_POST['status']);
        $created_at = date("Y-m-d H:i:s");

        $updatecategory = "UPDATE types SET
            `name` = '".$name."',
            `slug` = '".$slug."',
            `banner` = '".$banner."',
            `status` = '".$status."'
            WHERE `id` = '".$dataid."';
        ";

        $result = mysqli_query($conn, $updatecategory);
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
                alert("Category Updated Successfully");
                window.location.href='../contents.php';
            </script>
            <?php
        }
    ?>
</body>
</html>