<div id="scdd_erji">
  <div class="scdd_erji_up">
    商城订单
  </div>
  <div class="scdd_erji_down">
    <ul>
      <li>
        <a href="?s=order1&scene=0" <? if(empty($request['a']) && empty($request['scene'])){?>class="scdd_erji_down_on"<? }?>>当前订单</a>
      </li>
      <li>
        <a href="?s=order1&a=comment" <? if($request['a']=='comment'){?>class="scdd_erji_down_on"<? }?>>评价订单</a>
      </li>
      <!--<li>
        <a href="?s=order&a=guidang" <? if($request['a']=='guidang'){?>class="scdd_erji_down_on"<? }?>>归档订单</a>
      </li>-->
    </ul>
  </div>
</div>