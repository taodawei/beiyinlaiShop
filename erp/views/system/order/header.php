<div id="scdd_erji">
	<div class="scdd_erji_up">
	   <? if($request['if_jifen'] == 0){?>
       商城订单
       <? }else{ ?>
       积分订单
       <?}?>
   </div>
   <div class="scdd_erji_down">
       <ul>
          <li>
           <a href="?s=order&scene=0&if_jifen=<?=$request['if_jifen']?>" <? if(empty($request['a']) && empty($request['scene'])){?>class="scdd_erji_down_on"<? }?>>当前订单</a>
       </li>
       <!--<li>
           <a href="?s=order&scene=1" <? if(empty($request['a']) && $request['scene']==1){?>class="scdd_erji_down_on"<? }?>>货到付款订单</a>
       </li>-->
       <li>
           <a href="?s=order&scene=2&if_jifen=<?=$request['if_jifen']?>" <? if(empty($request['a']) && $request['scene']==2){?>class="scdd_erji_down_on"<? }?>>未支付订单</a>
       </li>
       <!--<li>
           <a href="?s=order&scene=3" <? if(empty($request['a']) && $request['scene']==3){?>class="scdd_erji_down_on"<? }?>>异常订单</a>
       </li>
       <li>
           <a href="?s=order&a=quehuo" <? if($request['a']=='quehuo'){?>class="scdd_erji_down_on"<? }?>>缺货订单</a>
       </li>
       <li>
           <a href="?s=order&a=yushou" <? if($request['a']=='yushou'||$request['a']=='yushou_order'){?>class="scdd_erji_down_on"<? }?>>预售订单</a>
       </li>-->
       <li style="display:none;" <? if($request['a']!='tuikuan_order' && $request['a']!='tuihuo_order' && $request['a']!='huanhuo_order'){?>onmouseenter="$(this).find('ul').stop().slideDown(100);" onmouseleave="$(this).find('ul').stop().slideUp(100);"<? }?>>
           <a href="javascript:" <? if($request['a']=='tuikuan_order'||$request['a']=='tuihuo_order'||$request['a']=='huanhuo_order'){?>class="scdd_erji_down_on"<? }?>>退换货订单</a>
           <ul <? if($request['a']=='tuikuan_order'||$request['a']=='tuihuo_order'||$request['a']=='huanhuo_order'){?>style="display:block"<? }?>>
              <li>
                  <a href="?s=order&a=tuikuan_order&type=1" <? if($request['a']=='tuikuan_order' && $request['type']==1){?>style="color:#1895d2"<? }?>>退款订单</a>
               </li>
               <li>
                  <a href="?s=order&a=tuikuan_order&type=2&if_jifen=<?=$request['if_jifen']?>" <? if($request['a']=='tuikuan_order' && $request['type']==2){?>style="color:#1895d2"<? }?>>退货退款</a>
               </li>
               <li>
                  <a href="?s=order&a=tuikuan_order&type=3&if_jifen=<?=$request['if_jifen']?>" <? if($request['a']=='tuikuan_order' && $request['type']==3){?>style="color:#1895d2"<? }?>>客户换货</a>
               </li>
           </ul>
       </li>
       <!--<li>
           <a href="?s=order&a=service" <? if($request['a']=='service'){?>class="scdd_erji_down_on"<? }?>>订单服务</a>
       </li>-->
       <li>
           <a href="?s=order&scene=7&if_jifen=<?=$request['if_jifen']?>" <? if(empty($request['a']) && $request['scene']==7){?>class="scdd_erji_down_on"<? }?>>完成订单</a>
       </li>
       <!--<li>
           <a href="?s=order&scene=8" <? if(empty($request['a']) && $request['scene']==8){?>class="scdd_erji_down_on"<? }?>>无效订单</a>
       </li>
       <li <? if($request['a']!='fapiao' && $request['a']!='caiwu_queren' && $request['a']!='tuikuan_queren'){?>onmouseenter="$(this).find('ul').stop().slideDown(100);" onmouseleave="$(this).find('ul').stop().slideUp(100);"<? }?>>
           <a href="javascript:" <? if($request['a']=='fapiao'||$request['a']=='caiwu_queren'||$request['a']=='tuikuan_queren'){?>class="scdd_erji_down_on"<? }?>>财务发票</a>
           <ul <? if($request['a']=='fapiao'||$request['a']=='caiwu_queren'||$request['a']=='tuikuan_queren'){?>style="display:block"<? }?>>
               <li>
                   <a href="?s=order&a=caiwu_queren" <? if($request['a']=='caiwu_queren'){?>style="color:#1895d2"<? }?>>付款确认</a>
               </li>
               <li>
                   <a href="?s=order&a=tuikuan_queren" <? if($request['a']=='tuikuan_queren'){?>style="color:#1895d2"<? }?>>退款确认</a>
               </li>
               <li>
                   <a href="?s=order&a=fapiao" <? if($request['a']=='fapiao'){?>style="color:#1895d2"<? }?>>发票管理</a>
               </li>
           </ul>
       </li>-->
       <? if($_SESSION['mendianId'] == 0){ ?>
       <li>
           <a href="?s=order&a=comment&if_jifen=<?=$request['if_jifen']?>" <? if($request['a']=='comment'){?>class="scdd_erji_down_on"<? }?>>评价订单</a>
       </li>
       <? } ?>
       <!--<li>
           <a href="?s=order&a=guidang" <? if($request['a']=='guidang'){?>class="scdd_erji_down_on"<? }?>>归档订单</a>
       </li>-->
   </ul>
</div>
</div>