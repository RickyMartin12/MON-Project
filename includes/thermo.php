<?php
//	Fund raising thermometer
//	author: Howard Griffin, September 2011.  Image courtesy of Pete Morelewicz
//	revised April 2013 to remove PCC specifics, its now as generic as I can make it.
//	image size default to 324x720, I use 150x333 on the web
//	scroll title at the top will be done via css from the web page,
//	current, goal, and startup are also supplied via the web page.

function LoadGif($current,$goal,$startup) {

// default values
	$imgname  = "/var/www/html/mon/img/thermo.gif";
	$img2name = "/var/www/html/mon/img/overgoal.gif";
	$img3name = "/var/www/html/mon/img/party.gif";	
	$width    = 324;		// size of original image
	$height   = 720;		// size of original image
	$font     = 7;			// from 1 (small) to 5 (largest)
	$overgoal = false;

// are you >= the goal?  if so images and markings are different
if ($current >= $goal) $overgoal = true;

// Open the default thermometer
	$im = @imagecreatefromgif($imgname);

// mark the starting amount
	$txt_color	= imagecolorallocate($im, 0, 0, 0);
	$show_startup = number_format($startup);
	imagestring( $im, $font,
		230,		//x coordinate
		573, 		//y coordinate, -5 centers the text on the top of the color bar
		$show_startup, $txt_color);	// string to write and color

		
		
	$im2 = @imagecreatefromgif($img3name);
	imagecopy($im, $im2, 	// destination, then source images
			25,5,			// x,y coordinate of destination point.
			0,0,			// x,y coordinate of source point. (0,0 is top left corner)
			268,102);		// Source width & height.
	imagedestroy($im2);		
		
		
		
		
		
if ($overgoal) {
	// Open the overflow image and copy it on top of the thermometer
	$im2 = @imagecreatefromgif($img2name);
	imagecopy($im, $im2, 	// destination, then source images
			107,80,			// x,y coordinate of destination point.
			0,0,			// x,y coordinate of source point. (0,0 is top left corner)
			105,64);		// Source width & height.
	imagedestroy($im2);
	}

// Raise or lower red color column by copying a nice looking chunk multiple times.
// how high the red goes is the % of the distance between $0 and the
// top of the thermometer
// y counts DOWN from the top, so a higher number is lower on the image.

// But first... if the current level is too low, then  erase part of the red.
// The red starts at 10%.
	if (($current-$startup) < ($goal-$startup)*0.1) {	// copy white image going down, erasing the red
								// which starts at 10% of the goal
		$top_mark = intval(573 - ($current-$startup)/($goal-$startup)*428);		// 428 is deff betw startup and goal
		for ($dest_y=535 ; $dest_y<$top_mark ; $dest_y++) {	// 535 is top of default red
			imagecopy($im, $im, // destination, then source images
				137,		// x-coordinate of destination point.
				$dest_y,	// y-coordinate of destination point.
				137,450,	// x,y coordinate of source point. (0,0 is top left corner)
				50,1);		// Source width & height.
		}
	}
	else {	// copy the red going up and stop at 100%
		$top_mark = intval(573 - ($current-$startup)/($goal-$startup)*428);
		if ($overgoal) $top_mark = 143;
		for ($dest_y=$top_mark ; $dest_y<=535 ; $dest_y=$dest_y+10) {
			imagecopy($im, $im, // destination, then source images
				137,		// x-coordinate of destination point.
				$dest_y,	// y-coordinate of destination point.
				137,536,	// x,y coordinate of source point. (0,0 is top left corner)
				50,10);		// Source width & height.
		}
	}


// mark the goal
	$txt_color	= imagecolorallocate($im, 0, 0, 0);	// black
	$show_goal = number_format($goal);
	if ($overgoal) $show_goal = number_format($current);
	imagestring( $im, $font,
		215, 135,					// xy coordinates of upper left
		$show_goal, $txt_color);	// string to write and color

// mark the current amount if not over
	if (!$overgoal) {
		$txt_color	= imagecolorallocate($im, 0, 0, 0);
		$show_current =  number_format($current);
		imagestring( $im, $font,
			215,			//x coordinate
			$top_mark - 5, 	//y coordinate, -5 centers the text vertically at the top of the bar
			$show_current, $txt_color);	// string to write and color
		}

// Add % of goal over BULB - works but difficult to read black over red
//	$txt_color	= imagecolorallocate($im, 0, 0, 0);
//	$pct	= sprintf( "%d%%", ($current/$goal)*100 );
//	imagestring( $im, $font,
//		($width/2)-((strlen($pct)/2)*ImageFontWidth($font+2)),			//x coordinate, of upper left
//		625,  //($height-($width/2))-(ImageFontHeight($font+2) / 2),	//y coordinate, hard coded, simpler
//		$pct, $txt_color);												// string to write and color

// Add % of goal next to BULB - easier to read
	$txt_color	= imagecolorallocate($im, 255, 255, 255);
//	$font = imageloadfont('../CenturyGothic.ttf');
	
	$pct	= sprintf( "%d%%", (($current-$startup)/($goal-$startup))*100 );
	imagestring( $im, 7,
		150,	//x coordinate, of upper left
		625,  	//y coordinate
		$pct, $txt_color);

// done!
	return $im;
	imagedestroy($im);
}	// end of function LoadGif















// defaults are set so if arguments are not supplied you do not throw an error
// arguments are often missing or invalid by submission from webcrawlers
	$startup = 0;
	$goal    = 100;
	$current = 10;


// read the arguments (via GET)
// via html it is:
// <img src="thermo.php?current=250&goal=1000&startup=0" width="150" height="333" alt="" />
	if (isset($_GET['startup'])) $startup = intval(trim($_GET['startup']));
	if (isset($_GET['goal']))    $goal = intval(trim($_GET['goal']));
	if (isset($_GET['current'])) $current = intval(trim($_GET['current']));


// ALL THREE arguments, and some sanity must be supplied
	if ($startup == '') $startup=0;
	if ($startup < 0) $startup=0;
	if ($goal <= 0) $goal = 0.1;	// cannot be zero else you get a divide-by-zero error

// call the graphing frnction
	header('Content-Type: image/gif');
	$img = LoadGif($current,$goal,$startup);
	imagegif($img);
	imagedestroy($img);