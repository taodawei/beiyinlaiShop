<?
global $db,$request;
require('phpqrcode.php');
$erweima = 'http://'.$_SERVER['HTTP_HOST'].'/z.php?id='.$request['id'].'&tuijianren='.$request['tuijianren'];
QRcode::png($erweima,'qr.png','L',8);
$QR = 'file/qr_'.$request['id'].'.png';
?>
<body align="center">
<img src="qr.png" width="300" id="ewmImg" />
</body>