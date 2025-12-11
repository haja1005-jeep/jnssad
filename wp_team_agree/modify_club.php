<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($code);

// 빈칸 체크 //
Input_Check($code);

// TB 필드명 //
$fields = array("name","comment");

// TB 필드값 //
$values = array("$name","$comment");

if($wp_passwd){
	// 비밀번호 설정 //
	$user_pw = Encrypt($wp_passwd);
	array_push($fields,"wp_passwd");
	array_push($values,"$user_pw");
}

// DB 저장 //
$insert = $Mysql->Update(wp_club_ctrl,$fields,$values," where wp_id='$Site_Admin'");

// 페이지 이동 //
echo ("
	<script>
		alert('정보가 정상적으로 수정되었습니다.');
	</script>
	<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=modify_club'>
	");
?>