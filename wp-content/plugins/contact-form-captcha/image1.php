<?php 
create_image();
function create_image(){
$switch = rand(1, 4);

if($switch==1) { 
	$r = rand(57, 62);
	$g = rand(122, 128);
	$b = rand(217, 223);
}
if($switch==2) { 
	$r = rand(207, 213);
	$g = rand(97, 103);
	$b = rand(97, 103);
}
if($switch==3) { 
	$r = rand(27, 33);
	$g = rand(192, 198);
	$b = rand(47, 53);
}
if($switch==4) { 
	$r = rand(187, 193);
	$g = rand(187, 193);
	$b = rand(17, 23);
}
$str=$_GET['q'];
$image = ImageCreate(96,24);
$src = imagecreatefrompng('gd'.$switch.'.png');
$textcolor = imagecolorallocate($src, $r, $g, $b);
imagestring($src, 14, 5, 1, $str, $textcolor);
imagecopyresampled($image, $src, 0, 0, 0, 0, 96, 24, 63, 18);
header('Content-Type: image/gif');
imagepng($image);
imagedestroy($image);
imagedestroy($src);
}
?>
