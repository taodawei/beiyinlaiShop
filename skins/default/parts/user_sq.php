<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$user = $db->get_row("select id,username,nickname,image,level,name,phone,wxh from users where id=$userId");
$level = array(1=>'普通', 2=>'团长',3=>'总监',4=>'联创');
?>
<div class="wode">
  <div class="wode_1">
      授权证书
        <div class="wode_1_left" onclick="location.href='/index.php?p=8&a=zhgl'">
          <img src="/skins/default/images/sousuo_1.png"/>
        </div>
    </div>
  <div class="shouquanzhengshu">
      <div class="shouquanzhengshu_up" style="position:relative;" id="zhongqiu">
          <img src="/skins/default/images/shouquanzhengshu_11.gif" style="position:absolute;top:0px;left:0px;width:100%;z-index:-1">
          <div class="shouquanzhengshu_up_1">
              <?=$user->name;?>
            </div>
          <div class="shouquanzhengshu_up_2">
              <?=$user->phone;?>
            </div>
          <div class="shouquanzhengshu_up_2">
              <?=$user->wxh;?>
            </div>
          <div class="shouquanzhengshu_up_3">
              <div class="shouquanzhengshu_up_3_left">
                  <?=$level[$user->level]?>
                </div>
              <div class="shouquanzhengshu_up_3_right">
                  NF<?=$user->id?>
                </div>
              <div class="clearBoth"></div>
            </div>
        </div>
      <div class="shouquanzhengshu_down">
          <a href="javascript:" onclick="layer.open({content:'长按上方图片保存',skin: 'msg',time: 2});"><img src="/skins/default/images/shouquanzhengshu_1.png"/> 保存</a>
        </div>
    </div>
</div>
<script type="text/javascript" src="/skins/demo/scripts/html2canvas.js"></script>
<script type="text/javascript">
  $(function(){
    var html = document.documentElement;
    var htmlWidth = html.getBoundingClientRect().width;
    if(htmlWidth>960)htmlWidth=960;
    var shareContent = document.getElementById("zhongqiu");
    var width = shareContent.offsetWidth; 
    var height = shareContent.offsetHeight; 
    var canvas = document.createElement("canvas"); 
    var scale = 2;
    canvas.width = width * scale;
    canvas.height = height * scale;
    canvas.getContext("2d").scale(scale, scale);
    html2canvas(shareContent,{
        useCORS:true,
        scale: scale,
        canvas: canvas,
        width: width,
        height: height
    }
    ).then(canvas => {
        layer.closeAll();
        var img_data1 = canvas.toDataURL();
        $("#zhongqiu").html('<img src="'+img_data1+'" width="100%">');
    });
  });
</script>