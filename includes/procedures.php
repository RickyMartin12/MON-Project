<?php

if($_GET['procedures']=="1")
{


if ($handle = opendir('procedures')) {

    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != ".." && $entry != "index.php"&& filetype('procedures/' . $entry)!="dir")
		{
			
			$file=explode(".",$entry)[0];
			echo "<a href=?procedures=".$file."> $file </a><br>";

        }
    }

    closedir($handle);
}
	
}

else
{
	$file=$_GET['procedures'];
    ?>
    <a href=?procedures=1>back to procedures</a><br>
    <?php

    echo "
        <div style=\"
        img {
            border: 3px solid #ff0000;
        }
        \" >
	";
	
	include "procedures/".$file.".php";
	
	echo "</div>";
}









