<!DOCTYPE html>
<?php
    require 'serverinfo.php';
?>
<html lang="en-ca">
    <head>
        <title>index-pointer_kaiju</title>
    </head>
    <body>
        <?php
            header("Location: ". $http_type. "://". $_SERVER['HTTP_HOST']. "/new");
        ?>
    </body>
</html>