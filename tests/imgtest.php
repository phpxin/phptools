<?php

require "../ImagickExt.class.php" ;




$imgObj = new ImagickExt() ;
$imgObj->font = '../test-resource/STXINGKA.TTF';
$imgObj->textBorderColor = 'white' ;
$imgObj->textColor = 'black' ;

$resultImg = $imgObj->waterImg('../test-resource/1.jpg','design by lixin', 160, 60 ,ImagickExt::POSITION_X_RIGHT,ImagickExt::POSITION_Y_BOTTOM );


header('Content-type: image/png');  
echo $resultImg; 