// JavaScript Document
/*分类下拉*/
$(document).ready(function(){
	$('.splist_up_01_left_01_up').click(function(){
		$('.splist_up_01_left_01_down').toggle();
	});
	
	$('.splist_up_01_left_02_up').click(function(){
		$('.splist_up_01_left_02_down').toggle();
	});
	
	$('.splist_up_01_right_2_up').click(function(){
		$('.splist_up_01_right_2_down').css({'top':'0','opacity':'1','visibility':'visible'});
		$('.bj1').css('display','block');
	});
	$('.bj1').click(function(){
		$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
		$('.bj1').css('display','none');
	});
	
	
	$('.title_input_01').click(function(){		
		$('.splist_up_01').toggle();
		$('.splist_up_02').toggle();
		var nowClass=$(this).attr('class'); 		
		if(nowClass=='title_input_02 title_line'){
			$(this).attr('class', 'title_input_01 title_line'); 
		}
		else if(nowClass=='title_input_01 title_line'){
			$(this).attr('class', 'title_input_02 title_line'); 
		}
	});
	
	$('.tt_input_01').click(function(){		
		$('.splist_up_01').toggle();
		$('.splist_up_02').toggle();
		var nowClass=$(this).attr('class'); 		
		if(nowClass == 'tt_input_02 tt_line'){
			$(this).attr('class', 'tt_input_01 tt_line'); 
		}
		else if(nowClass=='tt_input_01 tt_line'){
			$(this).attr('class', 'tt_input_02 tt_line'); 
		}
	});
	$('.yuandian').click(function(){
		$(this).next().toggle();	
	});
	
	$('.splist_down_right_up').click(function(){
		$('.xianshiziduan').animate({right:"0"});
	});
	$('.xianshiziduan_3_02').click(function(){
		$('.xianshiziduan').animate({right:"-360px"});	
	});	
	
	
});