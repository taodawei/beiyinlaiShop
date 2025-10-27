function viliUser(username){  

	xmlhttp=null; 

	if(window.XMLHttpRequest)

	{

        xmlhttp=new XMLHttpRequest();

    }

    else if(window.ActiveXObject)

	{

		try

		{

           xmlhttp=new ActiveXObject("Msxml2.XMLHttp");

        }

		catch(e)

		{

			try

			{

				xmlhttp=new ActiveXobject("Microsoft.XMLHttp");

			}

			catch(e)

			{

				alert("Your browser does not support AJAX!");

				return false;

			}

		}

	}
    xmlhttp.onreadystatechange = function (){

			if(xmlhttp.readyState == 4){

            	if(xmlhttp.status == 200){

                document.getElementById("authentication").innerHTML = xmlhttp.responseText ;
				if("恭喜！此用户名可以注册。"==xmlhttp.responseText){
					document.getElementById("authentication").style.color='green';
				}else{
					document.getElementById("authentication").style.color='red';
				}

            }   

        }

	}
    if(username.length>3 && username.length<17){
	 var url="/?m=user&a=checkusername&username="+escape(username);
     xmlhttp.open("GET",url,true) ;
     xmlhttp.send(null) ;
	}else{
	  	document.getElementById("authentication").innerHTML = "长度不符合标准";
		document.getElementById("authentication").style.color='red';
	}
}
function valiPass(password){
	if(password.length>5 && password.length<17){
		document.getElementById("errMsg_pwd").innerHTML = "密码符合标准";
		document.getElementById("errMsg_pwd").style.color='green';
	}else{
		document.getElementById("errMsg_pwd").innerHTML = "长度不符合标准";
		document.getElementById("errMsg_pwd").style.color='red';
	}
}
function valiRepass(password){
	if(password==document.getElementById("pwd").value){
		document.getElementById("repassMsg").innerHTML = "验证密码正确";
		document.getElementById("repassMsg").style.color='green';
	}else{
		document.getElementById("repassMsg").innerHTML = "与密码不符";
		document.getElementById("repassMsg").style.color='red';
	}
}
function valiEmail(email){
  var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
  if(reg.test(email)){
    document.getElementById("emailMsg").innerHTML = "Email格式正确";
	document.getElementById("emailMsg").style.color='green';
  }else{
    document.getElementById("emailMsg").innerHTML = "Email格式不正确";
	document.getElementById("emailMsg").style.color='red';
  }	
}
function valiTel(mtel){
  if(mtel.length!=8&&mtel.length!=11){
	document.getElementById("telMsg").innerHTML = "手机号码有误";
	document.getElementById("telMsg").style.color='red';
  }else{
	document.getElementById("telMsg").innerHTML = "手机号码正确";
	document.getElementById("telMsg").style.color='green';
  }
}