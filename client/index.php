<?php
    // redirecting...
    ignore_user_abort(true);
    header("Location: game.php", true);
    header("Connection: close", true);
    header("Content-Length: 0", true);
    ob_end_flush();
    flush();
//    fastcgi_finish_request(); // important when using php-fpm!
//*/    
   	require "../server/server.php";
?>
