$(document).ready(function () {
 
 //demo.showSwal('basic');
 /*
            var PaySel =  $("#rentalPaySel").val();
            var Pay    =  $("#FixPay").val();
			var A_PaySel = PaySel.split("|");  // split 함수사용..
			var Pay_money = parseInt(Pay) - parseInt(A_PaySel[0]);
				
			$("#rentalP").val(A_PaySel[1]); // 선택한 렌탈초기비용
			$("#rentalPay_money").val(Pay_money); // 실제주문 렌탈비용
			$("#rentalPay").text($.number(Pay_money)); // 화면에 보이는 렌탈비용
 */

 /* Start 2차종목 ********************************************************************************/
 /*  $('#gameList li').click(function() {
       
	   var gameCode  = $(this).data('code');
	   var gameValue = $(this).data('name');

	   $("#1thgame").text(gameValue);
	   $("#spo_code").val(gameCode);

       $('#gameList li').removeClass('btn-success');
       $(this).addClass('btn-success');


  $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&mode=spo_code", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#sports_sec_code').html(''); 
         $('#sports_sec_code').append(response); 

	 } 
     
   }); 

	
	return;


 });

/* End 2차 종목 ********************************************************************************/


/* Start 장애유형, 2차종목 Display**************************************************************/
$('#gameList li').click(function() {
       
	   var gameCode  = $(this).data('code');
	   var gameValue = $(this).data('name');

	   $("#spo_code_text").text(gameValue);
	   $("#spo_code").val(gameCode);

       $('#gameList li').removeClass('btn-success');

	   //alert(gameCode);


       $(this).addClass('btn-success');

	   //fn_btnChange_sex(gameCode); 종목별 남여혼합 표시
	   fn_Init_Form(); //Reset


	   /*
				$('#team_html').html('<h5>6.팀구분</h5>

			                       <div id='teamList'>

								        <li class="btn btn-fill-02" data-code ='A' data-name = 'A팀'>A팀</li>
                                        <li class="btn btn-fill-02" data-code ='B' data-name = 'B팀'>B팀</li>

                                    </div>');


       if(gameCode == 'GOALBALL') {
	   
					$('#GInfo_Text').html("<ul>
                                       <li>○ 감독과  코치 외 선수는 팀당 최소 3명 이상 최대 6명 이내에서 참가 </li>
									   <li>○ 시각장애의 특성상 보호자 참석필수</li>
                                     </ul>");   
	   
	   
	   }

*/

  $.ajax({

     //var params = jQuery("#sForm").serialize();

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&mode=trouble_code", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#trouble_code').html(''); 
         $('#trouble_code').append(response); 

	 } 
     
   }); 

	
	return;


 });

/* End 장애유형 ********************************************************************************/



/* Start 세부종목 ********************************************************************************/
$('#sexList li').click(function() {


	   var sexCode  = $(this).data('code');
	   var sexValue = $(this).data('name');


       $("#sex").val(sexCode);
	   $("#sex_text").text(sexValue);


       $('#sexList li').removeClass('btn-success');
       $(this).addClass('btn-success');


if($("#tro_level_code_input").val() == "") {
         demo.showSwal('tro_level_code');
} else {


       gameCode = $("#spo_code").val(); //선택종목
       troCode  = $("#tro_code_input").val();  //장애유형
       tro_level_Code  = $("#tro_level_code_input").val();  //장애등급

  $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&tro_code="+troCode+"&tro_level_code="+tro_level_Code+"&sex="+sexCode+"&mode=spo_code_detail", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#spo_code_detail_html').html(''); 
         $('#spo_code_detail_html').append(response); 

	 } 
     
   }); 
  
}
	
	return;
   
   
});






/* END 세부종목 ********************************************************************************/


   $('#teamList li').click(function() {

	   var teamCode  = $(this).data('code');
	   var teamValue = $(this).data('name');

	   if($("#spo_code").val() == "BOWLING" || $("#spo_code").val() == "ROWING") {
            
	   
	   } else {
	     
		 demo.showSwal('basic');
		 return false;
	   
	   }

       $("#group_str").val(teamCode);
	   $("#group_str_text").text(teamValue);


       $('#teamList li').removeClass('btn-success');
       $(this).addClass('btn-success');
   
   
   });




 $('#gameList2 li').click(function() {
       
	   var gameCode  = $(this).data('code');
	   var gameValue = $(this).data('name');

	   $("#spo_code_text").text(gameValue);
	   $("#spo_code1").val(gameCode);

       $('#gameList2 li').removeClass('btn-success');
       $(this).addClass('btn-success');

  });

  $('#join_status li').click(function() {

	   var join_statusCode  = $(this).data('code');
	   var join_statusValue = $(this).data('name');


       $("#join_status").val(join_statusCode);
	   $("#join_status_text").text(join_statusValue);


       $('#join_status li').removeClass('btn-success');
       $(this).addClass('btn-success');
   });





});


function chkFormValidation(frm) {


/* 감독, 코치, 인솔자 종목 선택 하지 않으면 에러 */
if(frm.kubun.value == '6' || frm.kubun.value == '7' || frm.kubun.value == '8') {

	 if(!frm.spo_code1.value){
		 demo.showSwal('spo_code');
         return false;
	 }

 } else {
    return true;
 } 


 if(frm.kubun.value == '9') {

	 if(!frm.spo_code.value){
		 demo.showSwal('spo_code');
         return false;
	 }


	 if(!frm.tro_code.value){
		 demo.showSwal('tro_code');
         return false;
	 }


	 if(!frm.tro_level_code.value){
		 demo.showSwal('tro_level_code');
         return false;
	 }


	 if(!frm.sex.value){
		 demo.showSwal('sex');
         return false;
	 }

	 if(!frm.spo_code_detail.value){
		 demo.showSwal('spo_code_detail');
         return false;
	 }
	 
 } else {
    return true;
 } 

    return true;
}




/* Start 종목별 남여구분 ********************************************************************************/
function fn_btnChange_sex(gameCode) {

   $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&mode=sex", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#sexList').html(''); 
         $('#sexList').append(response); 

	 } 
     
   }); 

	
	return;

}


/* END  종목별 남여구분 ********************************************************************************/


/* Start 스포츠등급 ********************************************************************************/
function fn_selectChange_tro_level_code() {

  var selectedVal  = $("#tro_code_sel option:selected").val();
  var selectedText = $("#tro_code_sel option:selected").text();

  $("#tro_code_text").text(selectedText);
  $("#tro_code_input").val(selectedVal);

  gameCode = $("#spo_code").val(); //선택종목

  fn_Init_Form2(); 


  $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&tro_code="+selectedVal+"&mode=tro_level_code", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#tro_level_code_html').html(''); 
         $('#tro_level_code_html').append(response); 

	 } 
     
   }); 

	
	return;


 };

/* End 스포츠등급 ********************************************************************************/

/* Start 세부종목 ********************************************************************************/
function fn_selectChange_spo_code_detail() {

  var selectedVal  = $("#tro_level_code_sel option:selected").val();
  var selectedText = $("#tro_level_code_sel option:selected").text();

  $("#tro_level_code_text").text(selectedText);
  $("#tro_level_code_input").val(selectedVal);

  gameCode = $("#spo_code").val(); //선택종목
  troCode =  $("#tro_code_input").val();  //장애유형

  fn_Init_Form3(); 

/*
  $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&tro_code="+troCode+"&tro_level_code="+selectedVal+"&mode=spo_code_detail", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#sports_code_detail1').html(''); 
         $('#sports_code_detail1').append(response); 

	 } 
     
   }); 
*/
	
	return;


 };

/* End 세부종목 ********************************************************************************/

/* 2차 종목 선택했을때 세부종목 ****************************************************************/
function fn_selectChange_spo_code_detail2() {

         var selectedVal  = $("#spo_sec_code option:selected").val();
         var selectedText = $("#spo_sec_code option:selected").text();

         //$("#spo_sec_code_text").text(selectedText);
         //$("#spo_sec_code_input").val(selectedVal);



        gameCode = $("#spo_code").val(); //선택종목
        troCode  = $("#tro_code_input").val();  //장애유형
        tro_level_Code  = $("#tro_level_code_input").val(); //장애등급
        sexCode  = $("#sex").val(); //성별

  $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&tro_code="+troCode+"&tro_level_code="+tro_level_Code+"&sex="+sexCode+"&spo_sec_code="+selectedVal+"&mode=spo_code_detail", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#spo_code_detail_html').html(''); 
         $('#spo_code_detail_html').append(response); 

	 } 
     
   }); 
  

	
	return;
   
   
}

/* 세부종목 선택했을때 화면 Display ****************************************************************/
function fn_selectChange_spo_code_detail_Display() {

         var selectedVal  = $("#spo_code_detail option:selected").val();
         var selectedText = $("#spo_code_detail option:selected").text();

         $("#spo_code_detail_text").text(selectedText);
         $("#spo_code_detail_input").val(selectedVal);

	


/*
  $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&tro_code="+troCode+"&tro_level_code="+tro_level_Code+"&sex="+sexCode+"&spo_sec_code="+selectedVal+"&mode=spo_code_detail", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#spo_code_detail_html').html(''); 
         $('#spo_code_detail_html').append(response); 

	 } 
     
   }); 
  
*/	
	return;
   
   
}


/* Start 종목선택하면 모든것 Reset**************************************************************/
function fn_Init_Form() {
      
	   $('#sexList li').removeClass('btn-success');
       $("#sex").val("");
	   $("#sex_text").text("");


	   $('#teamList li').removeClass('btn-success');
       $("#group_str").val("");
	   $("#group_str_text").text("");

       $("#tro_code_input").val("");
	   $("#tro_code_text").text("");


	   $('#tro_level_code_html').html(''); 

       $("#tro_level_code_input").val("");
	   $("#tro_level_code_text").text("");



       $("#spo_code_detail_input").val("");
	   $("#spo_code_detail_text").text("");


	   $('#spo_code_detail_html').html(''); 


}


function fn_Init_Form2() {
      
	   $('#sexList li').removeClass('btn-success');
       $("#sex").val("");
	   $("#sex_text").text("");


	   $('#teamList li').removeClass('btn-success');
       $("#group_str").val("");
	   $("#group_str_text").text("");



	   $('#tro_level_code_html').html(''); 

       $("#tro_level_code_input").val("");
	   $("#tro_level_code_text").text("");


       $("#spo_code_detail_input").val("");
	   $("#spo_code_detail_text").text("");

	   $('#spo_code_detail_html').html(''); 


}


function fn_Init_Form3() {
      
	   $('#sexList li').removeClass('btn-success');
       $("#sex").val("");
	   $("#sex_text").text("");


	   $('#teamList li').removeClass('btn-success');
       $("#group_str").val("");
	   $("#group_str_text").text("");

       $("#spo_code_detail_input").val("");
	   $("#spo_code_detail_text").text("");


	   $('#spo_code_detail_html').html(''); 


}
/* End 종목선택하면 모든것 Reset**************************************************************/





/* 2018년 2월 참가자 수정 장애유형 *****************************************************************************************************************************/
function fn_selectChange_mod_tro_code() {


  gameCode = $("#spo_code").val(); //선택종목
  tro_code_input = $("#tro_code_input").val(); //선택종목


  $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&tro_code="+tro_code_input+"&mode=trouble_code", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#trouble_code').html(''); 
         $('#trouble_code').append(response); 

	 } 
     
   }); 

	
	return;


 };

/* End 장애유형 ********************************************************************************/


/* 2018년 2월 참가자 수정 스포츠등급 ********************************************************************************/
function fn_selectChange_mod_tro_level_code() {


  gameCode = $("#spo_code").val(); //선택종목
  tro_code_input = $("#tro_code_input").val(); //장애유형
  tro_level_code_input = $("#tro_level_code_input").val(); //스포츠등급


  $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&tro_code="+tro_code_input+"&tro_level_code="+tro_level_code_input+"&mode=tro_level_code", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#tro_level_code_html').html(''); 
         $('#tro_level_code_html').append(response); 

	 } 
     
   }); 

	
	return;

 };

/* End 스포츠등급 ********************************************************************************/


/* 2018년 2월 참가자 수정 남여구분-오류로 인해 사용 안함******************************************/
function fn_selectChange_mod_sex() {


  gameCode = $("#spo_code").val(); //선택종목
  sex_input = $("#sex").val(); //성별


   $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&sex="+sex_input+"&mode=mod_sex", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   

         $('#sexList').append(response); 

	 } 
     
   }); 

	
	return;

 };

/* End 남여구분 ********************************************************************************/



/* 2018년 2월 참가자 수정 세부종목 ********************************************************************************/
function fn_selectChange_mod_spo_code_detail() {



   gameCode = $("#spo_code").val(); //선택종목
   spo_sec_Code = $("#spo_sec_code_input").val(); //선택종목
   troCode  = $("#tro_code_input").val();  //장애유형
   tro_level_Code  = $("#tro_level_code_input").val(); //장애등급
   sexCode  = $("#sex").val(); //성별
   spo_code_Detail = $("#spo_code_detail_input").val(); //세부종목




   $.ajax({

     type : "POST",
     async : true, 
     url : "input_ajax_2017.php",
     dataType : "html", 
     data : "spo_code="+gameCode+"&tro_code="+troCode+"&spo_sec_code="+spo_sec_Code+"&spo_code_details="+spo_code_Detail+"&tro_level_code="+tro_level_Code+"&sex="+sexCode+"&mode=spo_code_detail", 

     error : function(request, status, error) { 
       alert("code : " + request.status + "message : " + request.reponseText); 
     },
     contentType: "application/x-www-form-urlencoded; charset=UTF-8",
     success : function(response, status, request) { 
   
		 $('#spo_code_detail_html').html(''); 
         $('#spo_code_detail_html').append(response); 

	 } 
     
   }); 

	
	return;

 };

/* End 세부종목 ********************************************************************************/