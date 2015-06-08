<?php

/**
 * Simple function that calculates the *exact* bounding box (single pixel precision). 
 * The function returns an associative array with these keys: 
 * left, top:  coordinates you will pass to imagettftext 
 * width, height: dimension of the image you have to create 
 */
function calculateTextBox($text, $fontFile, $fontSize) {

    $rect = imagettfbbox($fontSize, 0, $fontFile, $text);
    $minX = min(array($rect[0], $rect[2], $rect[4], $rect[6]));
    $maxX = max(array($rect[0], $rect[2], $rect[4], $rect[6]));
    $minY = min(array($rect[1], $rect[3], $rect[5], $rect[7]));
    $maxY = max(array($rect[1], $rect[3], $rect[5], $rect[7]));

    return array(
        "left" => abs($minX) - 1,
        "top" => abs($minY) - 1,
        "width" => $maxX - $minX,
        "height" => $maxY - $minY,
        "box" => $rect
    );
}

//Decode the text
if (!isset($_GET["text"]) || empty($_GET["text"])) {
    $text = " ";
} else {
    $text = base64_decode(strrev(urldecode($_GET["text"]))); 
}

//Get the font size
if (!isset($_GET["size"]) || empty($_GET["size"])) {
    $size = 12;
} else {
    $size = min(max((int) $_GET["size"], 5), 32);
}

//The the padding
if (!isset($_GET["padding"])) {
    $padding = 2;
} else {
    $padding = min(max((int) $_GET["padding"], 0), 100);
}

//The bgcolor 0 to 255
if (!isset($_GET["bgcolor"])) {
    $bgcolorlevel = 245;
} else {
    $bgcolorlevel = min(max((int) $_GET["bgcolor"], 0), 255);
}

//The textcolor 0 to 255
if (!isset($_GET["textcolor"])) {
    $textcolorlevel = 50;
} else {
    $textcolorlevel = min(max((int) $_GET["textcolor"], 0), 255);
}


//is the background color tranparent?
$transparent = isset($_GET["transparent"]) && !empty($_GET["transparent"]);

$font = dirname(__FILE__) . '/arial.ttf'; //Font file must be on the same folder

if (!file_exists($font)) {
    die("The font file does not exist.");
}

$box = calculateTextBox($text, $font, $size);  //Dinamically calculate the text box
$width = $box["width"] + ($padding * 2);
$height = $box["height"] + ($padding * 2);

$image = imagecreate($width, $height) or die("Cannot Initialize new GD image stream. Please Install the PHP-GD extension.");

$bgcolor = imagecolorallocate($image, $bgcolorlevel, $bgcolorlevel, $bgcolorlevel); //Always gray scale
$textcolor = imagecolorallocate($image, $textcolorlevel, $textcolorlevel, $textcolorlevel); //Always gray scale

imagefill($image, $width, $height, $bgcolor); //Fill the background

if ($transparent) {
    imagecolortransparent($image, $bgcolor); //Set the bg color to transparent
}

//Write the text
imagettftext($image, $size, 0, $box["left"] + ($width / 2) - ($box["width"] / 2), $box["top"] + ($height / 2) - ($box["height"] / 2), $textcolor, $font, $text);

header("Content-Type: image/png");
imagepng($image);
imagedestroy($image);
