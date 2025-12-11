<?
// Mysql 클래스 //
require_once "../wp_library/mysql_class.php";
// DB 생성 //
$Mysql = new Mysql;

// DB 연결 //
$Mysql->Connect();

// Setup 설정 //
require_once "../wp_library/setup.php";
?>



<?
// 2차종목 선택
if($mode == 'spo_code' && $spo_code == 'ATHIETICS') {
?>

	<select multiple class="form-control" name="spo_sec_code" tabindex="2"  hname="경기2차종목" required  onchange="Trouble_Code(this.form); Init_Select(this.form, 'tro_level_code');" size='3' id='spo_sec_code'>


<?

// 배열 갯수 //
$c_que = "SELECT * FROM wp_sports_event_detail WHERE gam_uid='$gam_uid' AND spo_code = '$spo_code' AND isuse = '0' GROUP BY code ORDER BY code ASC, orderby ASC";
$c_res = mysql_query($c_que);
$num = mysql_num_rows($c_res);


// 레코드 결과 //
while($z_row = mysql_fetch_object($c_res))
{
	$code = $z_row->code; 
	$name = iconv("euckr", "utf8", $z_row->name);


	if($code)
	{
		 $selected = ($spo_sec_code == $code) ? "selected": "";
         $code_w .= "<option value='".$code."' ".$selected.">".$name."</option>";
         
         echo $code_w;
		 $code_w = "";

	}

}

?>
				
</select>

<?
} else if($mode == 'trouble_code') {

?>
<select multiple class="form-control" name="tro_code" tabindex="3" hname="장애유형"  onchange="fn_selectChange_tro_level_code();" size='7' id="tro_code_sel">


<?
// 배열 갯수 //
$c_que = "SELECT * FROM wp_game_event WHERE gam_uid='$gam_uid' AND spo_code = '$spo_code' GROUP BY tro_code ORDER BY tro_code ASC";
$c_res = mysql_query($c_que);
$num = mysql_num_rows($c_res);





// 레코드 결과 //
while($z_row = mysql_fetch_object($c_res))
{
	$code = $z_row->tro_code; 
	$name = iconv("euckr", "utf8", $wp_trouble_code[$z_row->tro_code]);



	if($code)
	{
		 $selected = ($tro_code == $code) ? "selected": ""; //2018.02.18일 수정 $spo_sec_code -> $tro_code
         $code_w .= "<option value='".$code."' ".$selected.">".$name."</option>";
         
         echo $code_w;
		 $code_w = "";

	}

}
?>
				
</select>

<?
//스포츠등급
} else if($mode == 'tro_level_code') {
?>

<select multiple class="form-control" name="tro_level_code" tabindex="5" hname="스포츠등급" onchange="fn_selectChange_spo_code_detail();" size='7' id="tro_level_code_sel">

<?
// 배열 갯수 //
$c_que = "SELECT * FROM wp_game_event WHERE gam_uid='$gam_uid' AND spo_code = '$spo_code' AND tro_code = '$tro_code' GROUP BY tro_level_code ORDER BY spo_code_detail ASC";
$c_res = mysql_query($c_que);
$num = mysql_num_rows($c_res);




// 레코드 결과 //
while($z_row = mysql_fetch_object($c_res))
{
	$code = iconv("euckr", "utf8", $z_row->tro_level_code);
	$name = iconv("euckr", "utf8", getTrouble_level_code($z_row->spo_code, $z_row->tro_level_code));


	if($code)
	{
		 //$selected = ($spo_sec_code == $code) ? "selected": "";
		 $selected = ($tro_level_code == $code) ? "selected": ""; //2018.02.18일 수정
         $code_w .= "<option value='".$code."' ".$selected.">".$name."(".$code.")</option>";
         
         echo $code_w;
		 $code_w = "";

	}

}
?>
				
</select>

<?

} else if($mode == 'sex') {


$query = "SELECT spo_code, sex FROM wp_game_event WHERE gam_uid='$gam_uid' AND spo_code = '$spo_code' GROUP BY sex";
$result = mysql_query($query);
$wp_game_sex = array();
while($obj = mysql_fetch_object($result))
{
	$wp_game_sex[$obj->sex] = $wp_sex_code[$obj->sex];
}


						foreach($wp_game_sex as $keys => $values)
						{
							if($keys == $sex)
								 $is_choice = "btn-success";
							else $is_choice = "";

								 echo $keys;
							
							$name = iconv("euckr","utf8",$values);
							echo "<li class='btn btn-fill-02 $is_choice' data-code ='$keys' data-name = '$name'>".$name."</li>";
						}

unset($query);
unset($result);
unset($obj);


} else if($mode == 'mod_sex') {

							if($sex == 'male')
								 $is_choice1 = "btn-success";
							if($sex == 'female')
								 $is_choice2 = "btn-success";
							if($sex == 'both')
								 $is_choice3 = "btn-success";
echo "
		  <li class='btn btn-fill-02 $is_choice1' data-code ='male'     data-name = '남자'>남자</li>
          <li class='btn btn-fill-02 $is_choice2' data-code ='female'   data-name = '여자'>여자</li>
          <li class='btn btn-fill-02 $is_choice3' data-code ='both'     data-name = '혼성'>혼성</li>
 ";


} else if($mode == 'spo_code_detail') { //세부종목 출력


/** 참가종목 수정 2018.02.18 **/
$sec = explode("|",$spo_sec_code);	//2차종목					
$scd = explode("|",$spo_code_details); //세부종목

$scdNum  = sizeof($scd); //세부종목 복수 선택 유무
/**************************************************** **/


if($spo_code == 'GOLF') $tro_level_code = iconv( "utf8", "euckr",$tro_level_code);

if($spo_code == 'ATHIETICS' || $spo_code == 'POWERLIFTING') {

	      $title2 = "5-2.세부종목";
		  $spo_sec_code = $sec[0]; //2차 종목이 배열로 넘어 온다. 2018.02.18
?>


   <h5>5-1. 2차 종목선택</h5>
   <!-- 2차종목 -->
   <select multiple class="form-control" name="spo_sec_code" required  size='2' id='spo_sec_code' onChange='fn_selectChange_spo_code_detail2()'>

<?

// 배열 갯수 //
$c_que = "SELECT * FROM wp_sports_event_detail WHERE gam_uid='$gam_uid' AND spo_code = '$spo_code' AND isuse = '0' GROUP BY code ORDER BY code ASC, orderby ASC";
$c_res = mysql_query($c_que);
$num = mysql_num_rows($c_res);






// 레코드 결과 //
while($z_row = mysql_fetch_object($c_res))
{
	$code = $z_row->code; 
	$name = iconv("euckr", "utf8", $z_row->name);

	


	if($code)
	{


		 if($spo_code == 'POWERLIFTING')  {
			 $selected = (in_array($code, $sec)) ? "selected": "";
         } else {
		     $selected = ($spo_sec_code == $code) ? "selected": "";
		 }



         $code_w .= "<option value='".$code."' ".$selected.">".$name."</option>";
         
		 if($spo_code == 'POWERLIFTING' && ($code == 'PL100' || $code == 'PL200' || $code == 'PL400' || $code == 'PL500'))  $code_w = ""; //역도 2차 종목 종합만 뜨게 2017.03.07추가    
         
		 
		 echo $code_w;
		 $code_w = "";

	}




}

?>
				
    </select>

<? } ?>



<h5><?=$title2?></h5>

<select multiple data-title="세부종목선택" name="spo_code_detail[]" class="selectpicker" data-style="btn-info btn-fill btn-block" data-menu-style="dropdown-blue"  id='spo_code_detail' onChange='fn_selectChange_spo_code_detail_Display();'> 

<?

if($spo_sec_code == '|') $spo_sec_code = "";  //정보 수정시 세부종목 여러개 일때 날라옴. 2018.02.16


//현재 육상만 적용함 2017.03.05
if($spo_sec_code && $spo_code == 'ATHIETICS') {

    // 배열 갯수 //
	$c_que = "SELECT * FROM wp_game_event WHERE gam_uid='$gam_uid' AND spo_code='$spo_code' AND spo_sec_code='$spo_sec_code' AND tro_code='$tro_code' AND sex='$sex' AND tro_level_code='".$tro_level_code."' ORDER BY spo_code_detail ASC";

} else {
    // 배열 갯수 //
	$c_que = "SELECT * FROM wp_game_event WHERE gam_uid='$gam_uid' AND spo_code='$spo_code' AND tro_code='$tro_code' AND sex='$sex' AND tro_level_code='".$tro_level_code."' ORDER BY spo_code_detail ASC";
}


	$c_res = mysql_query($c_que);
	$num = mysql_num_rows($c_res);


	// 세부종목 //
	$query = "SELECT code_detail,code_detail_name FROM wp_sports_event_detail WHERE gam_uid='$gam_uid' AND isuse='0' AND spo_code='$spo_code' AND sex='$sex' ORDER BY code_detail ASC";
    $result = mysql_query($query) or die( '['. mysql_error(). ']['  . $PHP_SELF . '][' . __LINE__ . ']');
    while($obj = mysql_fetch_object($result))
       {
	     $wp_sports_detail_code2[$obj->code_detail] = $obj->code_detail_name;
       }



// 레코드 결과 //
$ii = 0;
while($z_row = mysql_fetch_object($c_res))
{

			$code = iconv("euckr", "utf8", $z_row->spo_code_detail);
			$name = iconv("euckr", "utf8",$wp_sports_detail_code2[$z_row->spo_code_detail]);
	
     echo  $scd[$ii];
	 echo  $code;
	        	
			if($code) {
	
   
				  if($scdNum > 1) {
		
  				    //$selected = ($scd[$ii] == $code) ? "selected": "";  
		
                     $selected = (in_array($code, $scd)) ? "selected": "";
 				  
				  } else {
				  
				    $selected = ($spo_code_details == $code) ? "selected": "";

				  }
 
				  $code_w .= "<option value='".$code."' ".$selected.">".$name."</option>";
				  

                  echo $code_w;
				  $code_w = "";



	         }

			 $ii = $ii + 1;	 

	

}



/*

		// 레코드 결과 //
		while($z_row = mysql_fetch_object($c_res))
		{
			$code = $z_row->spo_code_detail;
			$name = iconv("euckr","utf8",$wp_sports_detail_code[$z_row->spo_code_detail]);

			if($code)
			{
				echo  "<label class='checkbox'><input type='checkbox' data-toggle='checkbox' name=\'spo_code_detail[]\' value='$code' >$name($code)</label>";

			}
		}



*/


?>

   </select>

<?
}
                            
                                                             
?>



 <!--   Core JS Files   -->

<!--  Forms Validations Plugin -->
	<script src="../v2/assets/js/jquery.validate.min.js"></script>

	<!--  Plugin for Date Time Picker and Full Calendar Plugin-->
	<script src="../v2/assets/js/moment.min.js"></script>

    <!--  Date Time Picker Plugin is included in this js file -->
    <script src="../v2/assets/js/bootstrap-datetimepicker.js"></script>

    <!--  Select Picker Plugin -->
    <script src="../v2/assets/js/bootstrap-selectpicker.js"></script>

	<!--  Checkbox, Radio, Switch and Tags Input Plugins -->
	<script src="../v2/assets/js/bootstrap-checkbox-radio-switch-tags.js"></script>

	<!--  Charts Plugin -->
	<script src="../v2/assets/js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="../v2/assets/js/bootstrap-notify.js"></script>

    <!-- Sweet Alert 2 plugin -->
	<script src="../v2/assets/js/sweetalert2.js"></script>

    <!-- Vector Map plugin -->
	<script src="../v2/assets/js/jquery-jvectormap.js"></script>



	<!-- Wizard Plugin    -->
    <script src="../v2/assets/js/jquery.bootstrap.wizard.min.js"></script>

    <!-- Light Bootstrap Dashboard Core javascript and methods -->
	<script src="../v2/assets/js/light-bootstrap-dashboard.js"></script>

	<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
	<script src="../v2/assets/js/demo.js"></script>
