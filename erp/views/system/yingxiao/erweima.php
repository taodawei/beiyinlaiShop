<?
global $db,$request;
require('phpqrcode.php');
$erweima = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?p=23&id='.$request['id'];
QRcode::png($erweima,'qr.png','L',8);
?>
<body align="center">
<img src="qr.png" width="300" id="ewmImg" />
</body>