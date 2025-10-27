<?php
class Paint
{
	private $sFile,$sPath,$sFileName,$ext;
	private $im;
	private $width,$height;
	private $isWatermark,$markX,$markY,$markStr;
	/**
	 * 构造函数，用来创建一个新的文件。
	 *
	 * @param string $sFile 要载入的文件
	 * @return Paint
	 */
	function __construct($sFile)
	{
		$this->Load($sFile);
	}
	function Load($sFile)
	{
		$this->sFile=$sFile;
		$this->prasePath();
		
		$filePath=$this->sPath.$this->sFileName;
		$filePath=$filePath[0]=='/'?ABSPATH.$filePath:$filePath;
		switch($this->ext){
			case "png":
				$this->im = imagecreatefrompng($filePath);
				break;
			case "gif":
				$this->im = imagecreatefromgif($filePath);
				break;
			case "bmp":
				$this->im = imagecreatefromwbmp($filePath);
				break;
			default:
				$this->im = imagecreatefromjpeg($filePath);
				break;
		}
		if(!$this->im){return false;}
		$this->width = imagesx($this->im);
		$this->height = imagesy($this->im);
		$this->isWatermark=false;
	}
	/**
	 * 缩放图像的方法
	 *
	 * @param int $sWidth 图像的宽
	 * @param int $sHeight 图像的高，可选，默认为宽的3/4
	 * @param bool $isFill 是否填充背景图 填充白色背景
	 * @param string $sOutFile 输出文件名 默认为 s_+原文件名
	 * @return unknown
	 */
	function Resize($sWidth,$sHeight=0,$prefix='',$isFill=true,$sOutFile='none')
	{
		$sHeight = $sHeight==0?$sWidth*3/4:$sHeight;
		
		if($sOutFile=='none')
		{
			$sOutFile=$this->sPath.$prefix.$this->sFileName;
		}
		
		$n_OriginalWidth 	= 	$this->width;
		$n_OriginalHeight	=	$this->height;
		if($isFill)
		{
			$newX=0;
			$newY=0;
			$ratio_orig=$n_OriginalWidth/$n_OriginalHeight;
			if($sWidth/$sHeight>$ratio_orig){
				$newHeight = $sHeight;
				$newWidth = $sHeight*$ratio_orig;
				$newY=0;
				$newX=($sWidth-($sHeight/$n_OriginalHeight)*$n_OriginalWidth)/2;
			}else{
				$newWidth = $sWidth;
				$newHeight = $newWidth*$ratio_orig;
				$newX=0;
				$newY=($sHeight-($sWidth/$n_OriginalWidth)*$n_OriginalHeight)/2;
			}
			if(($n_OriginalWidth<$newWidth) && ($n_OriginalHeight<$newHeight))
			{
				$newHeight = $sHeight = $n_OriginalHeight;
				$newWidth = $sWidth =  $n_OriginalWidth;
				$newX = 0;$newY = 0;
			}
			/*if($n_OriginalWidth>$n_OriginalHeight)
			{
				$newX=0;
				$newY=($sHeight-($sWidth/$n_OriginalWidth)*$n_OriginalHeight)/2;
				$newWidth=$sWidth;
				$newHeight=($sWidth/$n_OriginalWidth)*$n_OriginalHeight;
			}
			else
			{
				$newY=0;
				$newX=($sWidth-($sHeight/$n_OriginalHeight)*$n_OriginalWidth)/2;
				$newWidth=($sHeight/$n_OriginalHeight)*$n_OriginalWidth;
				$newHeight=$sHeight;
			}*/

			$newim = imagecreatetruecolor($sWidth, $sHeight);

		    $tempstr  = substr(paint_bgcolor,0,2).substr(paint_bgcolor,0,2);
		    $tempstr1 = (int)substr(paint_bgcolor,0,2).substr(paint_bgcolor,2,2);
		    $tempstr2 = (int)substr(paint_bgcolor,0,2).substr(paint_bgcolor,4,2);
		    $tempstr3 = (int)substr(paint_bgcolor,0,2).substr(paint_bgcolor,6,2);

			//设置颜色
			$grey = imagecolorallocate($newim, 0xff, 0xff, 0xff);
						
			imagefilledrectangle($newim,0,0,$sWidth, $sHeight,$grey);
			imagecopyresampled($newim, $this->im, $newX, $newY, 0, 0, $newWidth, $newHeight, $n_OriginalWidth, $n_OriginalHeight);
		}
		else {
			//判断一下 原图大小 和现在的图的大小 如果 原图小的话就用原图尺寸
			if($n_OriginalWidth > $n_OriginalHeight){
				$newWidth = $sWidth;
				$newHeight = ($sWidth / $n_OriginalWidth) * $n_OriginalHeight;
			}else{
				$newHeight = $sHeight;
				$newWidth = ($sHeight / $n_OriginalHeight) * $n_OriginalWidth;
			}
			if(($n_OriginalWidth<$newWidth) && ($n_OriginalHeight<$newHeight))
			{
				$newHeight = $n_OriginalHeight;
				$newWidth  =  $n_OriginalWidth;
			}
				$newim = imagecreatetruecolor($newWidth, $newHeight);
				imagecopyresampled($newim, $this->im, 0, 0, 0, 0, $newWidth, $newHeight, $n_OriginalWidth, $n_OriginalHeight);
		}
		if($this->isWatermark)
		{
			$black = imagecolorallocate($newim, 0, 0, 0);
			imagestring($newim,4,$this->markX,$this->markY,$this->markStr,$black);
		}
		$rsOutFile=$sOutFile;
		$sOutFile=$sOutFile[0]=='/'?ABSPATH.$sOutFile:$sOutFile;	
		switch($this->ext){
		case "png":
			imagepng($newim,$sOutFile);
			break;
		case "gif":
			imagegif($newim,$sOutFile);
			break;
		case "bmp":
			imagewbmp($newim,$sOutFile);
			break;
		default:
			imagejpeg($newim,$sOutFile,90);
			break;
		}

		imagedestroy($newim);
		return $rsOutFile;
	}
	/**
	 * 添加图像水印，
	 *
	 * @param string $str 水印的文字
	 * @param int $x 	x坐标位置
	 * @param int $y	y坐标位置
	 */
	function SetWatermark($str,$x,$y)
	{
		$isWatermark=true;
		$this->markX=$x;
		$this->markY=$y;
		$this->markStr=$str;

	}
	private function prasePath()
	{
		//提取路径与文件名
		if(preg_match('/(.*\/)(.*)/i',$this->sFile,$matchs))
		{
			$this->sPath=$matchs[1];
			$this->sFileName=$matchs[2];
		}
		else 
		{
			$this->sPath='';
		}
		$this->ext = substr(strrchr($this->sFileName, "."), 1);
	}
}
?>