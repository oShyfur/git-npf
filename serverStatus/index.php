<?php
    $starttime = microtime(true);
    
    echo '<h1>NP Backend server found. Thank you.</h1>';

    $endtime = microtime(true); // Bottom of page

    printf("Page loaded in %f seconds", $endtime - $starttime );    
?>