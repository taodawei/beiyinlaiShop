<div id="scdd_erji">
	<div class="scdd_erji_down">
		<ul>
			<li>
				<a class="scdd_erji_down_on">单据打印</a>
				<ul style="display:block;">
					<li>
						<a href="?s=yushou&yushouId=<?=$request['yushouId']?>">电子面单</a>
					</li>
					<li>
						<a href="?s=yushou&a=putong&yushouId=<?=$request['yushouId']?>">普通快递</a>
					</li>
				</ul>
			</li>
			<li>
				<a href="?s=yushou&a=yifahuo&yushouId=<?=$request['yushouId']?>" class="scdd_erji_down_on">配货</a>
			</li>
			<li>
				<a class="scdd_erji_down_on">发货确认</a>
				<ul style="display:block;">
					<li>
						<a href="?s=yushou&a=miandan_queren&yushouId=<?=$request['yushouId']?>">电子面单确认</a>
					</li>
					<li>
						<a href="?s=yushou&a=putong_queren&yushouId=<?=$request['yushouId']?>">普通快递导入</a>
					</li>
				</ul>
			</li>
			<li>
				<a class="scdd_erji_down_on">已发货</a>
				<ul style="display:block;">
					<li>
						<a href="?s=yushou&a=fhsuccess&yushouId=<?=$request['yushouId']?>">发货成功</a>
					</li>
					<li>
						<a href="?s=yushou&a=pici&yushouId=<?=$request['yushouId']?>">发货批次</a>
					</li>
				</ul>
			</li>
			<li>
				<a class="scdd_erji_down_on">发货异常处理</a>
				<ul style="display:block;">
					<li>
						<a href="?s=yushou&a=zanting&yushouId=<?=$request['yushouId']?>">暂停发货</a>
					</li>
					<li>
						<a href="?s=yushou&a=chexiao&yushouId=<?=$request['yushouId']?>">撤销发货</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</div>