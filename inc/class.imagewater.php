<?php
class WaterPrint
{
//类开始
    public $text, $color, $size, $font, $angle, $px, $py, $im ,$picpath;
//要添加的文字 
public function GetWpText($text)
    {
   $this->text = $text;
    }
//添加文字的颜色
public function GetFtColor($color)
    {
   $this->color = $color;
    }
//添加文字的字体
public function GetFtType($font)
    {
   $this->font = $font;
    }
  
//添加文字的大小
public function GetFtSize($size)
    {
   $this->size = $size;
    }
//文字旋转的角度
public function GetTtAngle($angle)
    {
   $this->angle = $angle;
    }
//添加文字的位置
public function GetTtPosit()
    {
    $temp = imagettfbbox(ceil($this->size),0,$this->font,$this->text);//取得使用 TrueType 字体的文本的范围   
    $w = $temp[2] - $temp[6];   
    $h = $temp[3] - $temp[7];
	$ground_info = getimagesize($this->picpath);
    $ground_w    = $ground_info[0];//取得背景图片的宽
    $ground_h    = $ground_info[1];
	$posX = rand(0,($ground_w - $w));
    $posY = rand(0,($ground_h - $h));
	echo $w.'-'.$ground_w;
	require_once(dirname(__FILE__).'/../'."config/dt-config.php");
	switch(SYPOS)
        {   
            case 0://随机   
                $posX = rand(0,($ground_w - $w));   
                $posY = rand(0,($ground_h - $h));   
                break;   
            case 1://1为顶端居左   
                $posX = 0;   
                $posY = 0;   
                break;   
            case 3:
                $posX = $ground_w - $w;   
                $posY = 0;   
                break; 
            case 5://5为中部居中   
                $posX = ($ground_w - $w) / 2;   
                $posY = ($ground_h - $h) / 2;   
                break;  
            case 7://7为底端居左   
                $posX = 0;   
                $posY = $ground_h - $h;   
                break;
            case 9://9为底端居右   
                $posX = $ground_w - $w;
                $posY = $ground_h - $h;   
                break;
        }
   $this->px = $posX;
   $this->py = $posY;
    }    
//添加文字水印 
public function AddWpText($pict)
    {
    $ext = substr($pict,strlen($pict)-3);
	$this->picpath = $pict;
    switch ($ext) {
   case 'gif':
       $picext = "gif";
    $this->im = imagecreatefromgif($pict);
    break;
   case 'jpg':
       $picext = "jpg";
    $this->im = imagecreatefromjpeg($pict);
    break;
   case 'png':
       $picext = "png";
    $this->im = imagecreatefrompng($pict);
    break;
   default:
       $this->Errmsg('类型不符合');
    break;
   }
   //$this->picext = $picext;
   $im   = $this->im;
   $font = $this->font;
   $size = $this->size;
   $this->GetTtPosit();
   
   $angle= $this->angle;
   $px   = $this->px;
   $py   = $this->py;
   $color= $this->color;
   $text = $this->text;
   @require_once(dirname(__FILE__).'/../'."config/dt-config.php");
   $sy = str_replace('rgb(','',SYCOLOR);
   $sy = str_replace(')','',$sy);
   $colors = explode(',',$sy);
   $color= imagecolorallocate($im,(int)trim($colors[0]),(int)trim($colors[1]),(int)trim($colors[2]));
   imagettftext($im, $size, $angle, $px, $py, $color, $font, $text);
   switch ($picext) {
   case "gif":
       imagegif($im, $pict);
    break;
   case "jpg":
       imagejpeg($im, $pict, 100);
    break;
   case "png":
      imagealphablending($im, false);
            imagesavealpha($im, true);
       imagepng($im, $pict);
    break;
   }
   imagedestroy($im);
    }

//错误信息提示 
public function Errmsg($msg)
    {
        echo $msg;
    }
//类结束 
}
?>