<?php
$path = "/var/www/html/mon_producao/leads/9365/";
$file_name = "/var/www/html/mon_producao/leads/9365/Diagrama_DANIEL_SUP.pdf";

$file_im = $file_name."[0]"; 
$im = new Imagick();
$im->setResolution(300, 300);     //set the resolution of the resulting jpg

try {
    $im->readImage($file_im);    //[0] for the first page
    $file_name = preg_replace("/.pdf/", '_pdf', $file_name);
				$full_file = $file_name;
				$im->setImageFilename($full_file.".png");
                $im->writeImage();
} catch(ImagickException $e) {
    echo "Error: " . $e -> getMessage() . "\n";
}


/*
function readInput() {
	$file = 'add2.png';

	if (!file_exists($file)) {
		copy('http://upload.wikimedia.org/wikipedia/commons/4/47/PNG_transparency_demonstration_1.png', $file);
	}

	$im = new Imagick();
	$im->readImage($file);

	$im->setImageFormat('JPEG');
	$im->setImageCompression(\Imagick::COMPRESSION_JPEG);

	return $im;
}

// write directly to file with .jpg extension
$im = readInput();
try {
    $im->writeImage('teste_1.jpg');
} catch(ImagickException $e) {
    echo "Error: " . $e -> getMessage() . "\n";
}
*/


// less test-writeimage.jpg:
// test-writeimage.jpg JPEG 800x600 800x600+0+0 8-bit DirectClass 47.7KB 0.000u 0:00.000


?>