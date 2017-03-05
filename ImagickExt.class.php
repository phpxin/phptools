<?php
/**
 * php图片处理扩展
 * 依赖ImageMagick，Imagick扩展
 */

class ImagickExt{
	private $font ;
	private $fontSize = 30 ;
	private $textColor ;
	private $textBorderColor ;
	
	
	const POSITION_X_LEFT = 'x-left' ;
	const POSITION_X_CENTER = 'x-center' ;
	const POSITION_X_RIGHT = 'x-right' ;
	const POSITION_Y_TOP = 'y-top' ;
	const POSITION_Y_CENTER = 'y-center' ;
	const POSITION_Y_BOTTOM = 'y-bottom' ;
	
	
	
	public function __get($name){
		return $this->$name ;
	}
	
	public function __set($name, $value){
		
		$this->$name = $value ;
		
	}
	
	public function __construct(){
		
		
	}
	
	/**
	 * 给图片增加文字水印
	 * @param string $filepath 原图片路径
	 * @param string $text 水印文字
	 * @param float $waterWidth 水印宽度
	 * @param float $waterHeight 水印高度
	 * @param mixed $x 水印x轴位置，数字为自定义位置，可以使用类常量 POSITION_X_xx
	 * @param mixed $y 水印y轴位置，数字为自定义位置，可以使用类常量 POSITION_Y_xx
	 * @return Imagick 图片资源，可以直接通过echo输出
	 */
	public function waterImg($filepath, $text, $waterWidth, $waterHeight, $x=self::POSITION_X_LEFT, $y=self::POSITION_Y_TOP){
		$srcImg = new Imagick ($filepath);

		$waterImg = new Imagick();  

		$draw = new ImagickDraw();  
		$pixel = new ImagickPixel( 'gray' );  
		$waterImg->newImage($waterWidth, $waterHeight, $pixel);  
		$waterImg->setImageOpacity(0);
		if(!$this->font){
			throw new ImagickExtException("font is null");
		}
		$draw->setFont($this->font);  
		if($this->textBorderColor){
			$draw->setStrokeColor ( new ImagickPixel( 'white' ) );  //设置画笔颜色，默认无颜色
		}
		if($this->textColor){
			$draw->setFillColor ( new ImagickPixel( 'black' ) );  //设置填充颜色，默认黑色
		}
		$draw->setFontSize( $this->fontSize );  
		$waterImg->annotateImage($draw, 10, $this->fontSize, 0, $text);  
		$waterImg->setImageFormat('png');  

		if(is_string($x)){
			$srcWidth = $srcImg->getImageWidth();
			if($x == self::POSITION_X_CENTER){
				$positionX = $srcWidth/2 - $waterWidth/2;
			}else if($x == self::POSITION_X_LEFT){
				$positionX = 0;
			}else if($x == self::POSITION_X_RIGHT){
				$positionX = $srcWidth-$waterWidth;
			}else{
				throw new ImagickExtException("unknown position x");
			}
		}else{
			$positionX = $x ;
		}
		
		if(is_string($y)){
			$srcHeight = $srcImg->getImageHeight();
			if($y == self::POSITION_Y_CENTER){
				$positionY = $srcHeight/2 - $waterHeight/2;
			}else if($y == self::POSITION_Y_TOP){
				$positionY = 0;
			}else if($y == self::POSITION_Y_BOTTOM){
				$positionY = $srcHeight-$waterHeight;
			}else{
				throw new ImagickExtException("unknown position y");
			}
		}else{
			$positionY = $y ;
		}
		
		
		$srcImg->compositeImage ( $waterImg,imagick::COMPOSITE_OVER, $positionX, $positionY );
		
		return $srcImg;
	}
	
	
}

class ImagickExtException extends Exception{
	
}

?>