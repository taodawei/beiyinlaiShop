<?
global $request;
?>
<div class="huiyuanxinxi_up">
    <ul>
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=basic&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '. ($request['a']=='basic'?'class="huiyuanxinxi_up_on"':''||$request['a']=='edit_basic'?'class="huiyuanxinxi_up_on"':'').'>基本资料</a></li>') ?>
        
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=safe&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '.($request['a']=='safe'?'class="huiyuanxinxi_up_on"':'').'>账户安全</a></li>') ?>
        
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=liushui&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '.($request['a']=='liushui'?'class="huiyuanxinxi_up_on"':'').'>余额明细</a></li>') ?>
        
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=yongjinInfo&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '.($request['a']=='yongjinInfo'?'class="huiyuanxinxi_up_on"':'').'>佣金明细</a></li>') ?>
        
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=jifen_jilu&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '.($request['a']=='jifen_jilu'?'class="huiyuanxinxi_up_on"':'').'>积分明细</a></li>') ?>
        
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=yhq&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '.($request['a']=='yhq'?'class="huiyuanxinxi_up_on"':'').'>优惠券</a></li>') ?>
        
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=card&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '.(($request['a']=='card' || $request['a']=='cardLiuShui')?'class="huiyuanxinxi_up_on"':'').'>储值卡</a></li>') ?>
        
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=order_jilu&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '.($request['a']=='order_jilu'?'class="huiyuanxinxi_up_on"':'').'>消费明细</a></li>') ?>
        
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=fans&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '.($request['a']=='fans'?'class="huiyuanxinxi_up_on"':'').'>下级会员</a></li>') ?>
        
        <? chekurl($arr,'<li><a href="?m=system&s=users&a=operate&id='.$request['id'].'&returnurl='.urlencode($request['returnurl']).'" '.($request['a']=='operate'?'class="huiyuanxinxi_up_on"':'').'>操作日志</a></li>') ?>
        
        <div class="clearBoth"></div>
    </ul>
</div>