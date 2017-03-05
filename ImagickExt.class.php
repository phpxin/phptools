<?php
/**
 * phpͼƬ������չ
 * ����ImageMagick��Imagick��չ
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
	 * ��ͼƬ��������ˮӡ
	 * @param string $filepath ԭͼƬ·��
	 * @param string $text ˮӡ����
	 * @param float $waterWidth ˮӡ���
	 * @param float $waterHeight ˮӡ�߶�
	 * @param mixed $x ˮӡx��λ�ã�����Ϊ�Զ���λ�ã�����ʹ���ೣ�� POSITION_X_xx
	 * @param mixed $y ˮӡy��λ�ã�����Ϊ�Զ���λ�ã�����ʹ���ೣ�� POSITION_Y_xx
	 * @return Imagick ͼƬ��Դ������ֱ��ͨ��echo���
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
			$draw->setStrokeColor ( new ImagickPixel( 'white' ) );  //���û�����ɫ��Ĭ������ɫ
		}
		if($this->textColor){
			$draw->setFillColor ( new ImagickPixel( 'black' ) );  //���������ɫ��Ĭ�Ϻ�ɫ
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