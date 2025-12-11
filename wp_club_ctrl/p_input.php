<?
// 클래스 //
require_once "../wp_object/head_pop.html";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($wp_id);
Null_Check($wp_passwd);

// 빈칸 체크 //
Input_Check($wp_id);
Input_Check($wp_passwd);

// 레벨 지정 //
$level = 9; // 관리가능

// 경기단체는 아이디와 담당 지역이 고정이다. //
if($auth == "s_group")
{
	$wp_id = $s_id;
	$city = "S00";
	$level = 1;
}

// 시군 코드 지정 //
$code = $wp_city_code[$city];
$name = $wp_city_con_code[$city];

// 비밀번호 설정 //
$user_pw = Encrypt($wp_passwd);

// 중복처리 쿼리 //
$c_que = "SELECT * FROM wp_club_ctrl WHERE wp_id = '$wp_id'";
$c_res = mysql_query($c_que);
$c_cnt = mysql_num_rows($c_res);

if($c_cnt > 0)
{
	echo ("
		<script language='javascript'>
		<!--//
		alert('$wp_id 는 이미 등록되어있는 아이디 입니다. 다른 아이디를 입력하십시오.');
		//-->
		</script>
		<meta http-equiv='Refresh' content='0; URL=p_input.html'>
	");
}
else
{
	// TB 필드명 //
	$fields = array("code","name","city","wp_id","wp_passwd","auth","level","isuse","comment","status","r_name","hp1","hp2","hp3","email","signdate");

	// TB 필드값 //
	$values = array("$name","$code","$city","$wp_id","$user_pw","$auth","$level","0","$comment","0","$r_name","$r_hp1","$r_hp2","$r_hp3","$r_email","$signdate");

	// DB 저장 //
	$insert = $Mysql->Insert(wp_club_apply,$fields,$values);

	echo ("
		<script language='javascript'>
		<!--//
		alert('$r_name 님 성공적으로 신청되었습니다.');
		//-->
		</script>
		<meta http-equiv='Refresh' content='0; URL=p_input.html'>
	");
}


/*
// 사용자 확인 체크
$que = "SELECT * FROM wp_club_apply WHERE city = '$name' AND name = '$r_name' AND (hp1 = '$r_hp1' AND hp2 = '$r_hp2' AND hp3 = '$r_hp3') AND email = '$r_email'";
$res = mysql_query($que);
$cnt = mysql_num_rows($res);

if($cnt > 0)
{
	// 비밀번호 설정 //
	$user_pw = Encrypt($wp_passwd);

	// TB 필드명 //
	$fields = array("code","name","city","wp_id","wp_passwd","auth","level","isuse","comment","status");

	// TB 필드값 //
	$values = array("$name","$code","$city","$wp_id","$user_pw","$auth","$level","0","$comment","0");

	// DB 저장 //
	$insert = $Mysql->Insert(wp_club_ctrl,$fields,$values);
	exit;
	echo ("
		<script language='javascript'>
		<!--//
		//alert('$r_name 님 성공적으로 신청되었습니다.');
		//-->
		</script>
		<meta http-equiv='Refresh' content='0; URL=p_input.html'>
	");
}
else
{
	exit;
	echo ("
		<script language='javascript'>
		<!--//
		//alert('$r_name 님의 담당자 정보가 등록되어 있지 않습니다.');
		//history.back(-1);
		//-->
		</script>
	");
}
*/
?>