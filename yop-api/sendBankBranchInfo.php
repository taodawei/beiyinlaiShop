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



function bankBranchInfo(){
      
      
	   global $private_key;
	   global $yop_public_key;
    global $appKey;
    $request = new YopRequest($appKey, $private_key);
    
    $request->addParam("headBankCode", $_REQUEST['headBankCode']);
    $request->addParam("provinceCode", $_REQUEST['provinceCode']);
    $request->addParam("cityCode", $_REQUEST['cityCode']);
     
 
 
 
    $response = YopClient3::post("/rest/v1.0/sys/merchant/bankbranchInfo", $request);
	//print_r($response);
    if($response->validSign==1){
        echo "返回结果签名验证成功!\n";
    }
    //取得返回结果
    $data=object_array($response);
    
    return $data;
    
 }
  $array=bankBranchInfo();  
   
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
<title> 获取支行信息接口--返回参数 </title>
</head>
	<body>	
		<br /> <br />
		<table width="70%" border="0" align="center" cellpadding="5" cellspacing="0" style="border:solid 1px #107929">
			<tr>
		  		<th align="center" height="30" colspan="5" bgcolor="#6BBE18">
					获取支行信息接口--返回参数 
				</th>
		  	</tr>
	<tr >
				<td width="25%" align="left">&nbsp;银行信息</td>
				<td width="5%"  align="center"> : </td> 
				<td width="45"  align="left"> <textarea id="fileInfo" style="width: 80%;" name="fileInfo" rows="15"  ><?php print_r( $result['branchBankInfo']);?> </textarea></td>
				 
				 
			</tr>

			 

		</table>

	</body>
</html>