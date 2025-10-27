<?php
include 'conf.php';
require_once ("./lib/YopClient3.php");
 


function object_array($array) { 
    if(is_object($array)) { 
        $array = (array)$array; 
     } if(is_array($array)) { 
         foreach($array as $key=>$value) { 
             $array[$key] = object_array($value); 
             } 
     } 
     return $array; 
}



function regstatusquery(){
   
	 global $merchantNo;
     global $parentMerchantNo;
	 global $private_key;
	 global $yop_public_key;

    global $appKey;
    $request = new YopRequest($appKey, $private_key);
    $request->addParam("parentMerchantNo", $parentMerchantNo);
    $request->addParam("merchantNo", $_REQUEST['merchantNo']);
   
 
    $response = YopClient3::post("/rest/v1.0/sys/merchant/regstatusquery", $request);
    if($response->validSign==1){
        //echo "返回结果签名验证成功!\n";
    }
    //取得返回结果
    $data=object_array($response);
    
    return $data;
    
 }
  $array=regstatusquery();  
   
 if( $array['result'] == NULL)
 {
 	echo "error:".$array['error'];
  return;}
 else{
 $result= $array['result'] ;
  
}
?> 


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title> 入网信息查询 </title>
</head>
	<body>	
		<br /> <br />
		<table width="70%" border="0" align="center" cellpadding="5" cellspacing="0" style="border:solid 1px #107929">
			<tr>
		  		<th align="center" height="30" colspan="5" bgcolor="#6BBE18">
					入网信息查询
				</th>
		  	</tr>
            <tr >
				<td width="25%" align="left">&nbsp;请求返回码</td>
				<td width="5%"  align="center"> : </td> 
				<td width="45"  align="left"> <?php echo $result['returnCode'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">returnCode</td> 
			</tr>

			<tr>
				<td width="25%" align="left">&nbsp;请求返回信息</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo  $result['returnMsg'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">returnMsg</td> 
			</tr> 
 
            <!--<tr >
				<td width="25%" align="left">&nbsp;代理商编号</td>
				<td width="5%"  align="center"> : </td> 
				<td width="45"  align="left"> <?php echo $result['parentMerchantNo'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">parentMerchantNo</td> 
			</tr>-->

			<tr>
				<td width="25%" align="left">&nbsp;商户编号</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo  $result['merchantNo'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">merchantNo</td> 
			</tr>
 
			<tr>
				<td width="25%" align="left">&nbsp;入网请求号</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo  $result['requestNo'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">requestNo</td> 
			</tr>
			 <tr>
				<td width="25%" align="left">&nbsp;内部流水号</td>
				<td width="5%"  align="center"> : </td> 
				<td width="35%" align="left"> <?php echo  $result['externalId'];?> </td>
				<td width="5%"  align="center"> - </td> 
				<td width="30%" align="left">externalId</td> 
			</tr>
			<tr>
				<td width="25%" align="left">&nbsp;商户入网状态</td>
				<td width="5%"  align="center"> : </td> 
                 <td width="35%" align="left"> <?php echo $result['merNetInOutStatus'];?>(<font color="red"><?
                 	switch ($result['merNetInOutStatus']) {
                 		case 'INIT':
                 			echo '待审核';
                 		break;
                 		case 'PROCESSING':
                 			echo '审核中';
                 		break;
                 		case 'PROCESS_SUCCESS':
                 			echo '审核通过';
                 		break;
                 		case 'PROCESS_REJECT':
                 			echo '审核拒绝';
                 		break;
                 		case 'PROCESS_BACK':
                 			echo '审核回退';
                 		break;
                 		case 'PROCESSING_PRODUCT_INFO _ SUCCESS':
                 			echo '产品提前开通';
                 		break;
                 		default:
                 			echo '无';
                 		break;
                 	}
                 	?></font>) </td> 			
				 <td width="5%"  align="center"> - </td> 
				<td width="30%" align="left"><?
				if($result['merNetInOutStatus']=='INIT'){
					?><a href="authorizenum.php?merchantNo=<?=$result['merchantNo']?>">重发短信验证</a><?
				}else if($result['merNetInOutStatus']=='PROCESS_REJECT' || $result['merNetInOutStatus']=='PROCESS_BACK'){
					?><a href="/erp/index.php?s=dinghuo_set&a=yibao&re=1&requestNo=<?=$result['requestNo'];?>">重发申请</a><?
				}
				?></td> 
			</tr>
			<?
			if(!empty($result['merchantNo'])){
				$request = new YopRequest($appKey, $private_key);
				$request = new YopRequest($appKey, $private_key);
			    $request->addParam("parentMerchantNo", $parentMerchantNo);
			    $request->addParam("merchantNo", $result['merchantNo']);
			    $response = YopClient3::post("/rest/v1.0/sys/merchant/balancequery", $request);
			    //取得返回结果
			    //var_dump($response);
			    $data=object_array($response);
				?>
				<tr>
					<td width="25%" align="left">&nbsp;结算银行卡修改</td>
					<td width="5%"  align="center"> : </td> 
	                 <td width="35%" align="left">  <a href="sms.php?requestNo=<?=$result['requestNo']?>&merchantNo=<?=$result['merchantNo']?>">修改结算银行卡</a></td>
					 <td width="5%"  align="center"> - </td> 
					<td width="30%" align="left"></td> 
				</tr>
				<tr>
					<td width="25%" align="left">&nbsp;商户余额</td>
					<td width="5%"  align="center"> : </td> 
	                 <td width="35%" align="left">  <?=number_format($data['result']['merBalance'],2)?></td>
					 <td width="5%"  align="center"> - </td> 
					<td width="30%" align="left"><a href="https://mp.yeepay.com/auth/signin" target="_blank">去商户平台</a></td> 
				</tr>
				<?
				}
			?>
		</table>

	</body>
</html>