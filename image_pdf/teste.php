<?php

    require __DIR__.'/vendor/autoload.php';
    

    $imagick = new Imagick();

    $imagick->readImage('myfile.pdf[0]');

    $imagick->writeImages('converted.png');

?>