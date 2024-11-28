<html>
    
    <body>
        <?php
        $connect = mysqli_connect ("localhost", "root",
        "","evoting");

        if(!$connect)
        {
            die('ERROR:' .mysqli_connect_error());
        }

        ?>
    </body>
</html>