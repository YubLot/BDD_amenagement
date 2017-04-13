<?php

function creer_miniatures($src, $dest, $desired_width) {

	/* read the source image */
	$image_origine = imagecreatefromjpeg($src);
	$width = imagesx($image_origine);
	$height = imagesy($image_origine);
	
	/* find the "desired height" of this thumbnail, relative to the desired width  */
	$desired_height = floor($height * ($desired_width / $width));
	
	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
	
	/* copy source image at a resized size */
	imagecopyresampled($virtual_image, $image_origine, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
	
	/* create the physical thumbnail image to its destination */
	imagejpeg($virtual_image, $dest);
}

chdir("photos");
foreach(glob('*.*') as $photo) {
	creer_miniatures($photo, "thumbs/". $photo, 300);
}

?>