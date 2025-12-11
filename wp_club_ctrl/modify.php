<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($city);
Null_Check($code);

// 빈칸 체크 //
Input_Check($city);
Input_Check($code);

// TB 필드명 //
$fields = array("code","name","city","wp_id","auth","level","isuse","comment");

// TB 필드값 //
$values = array("$code","$name","$city","$wp_id","$auth","$level","$isuse","$comment");

if($wp_passwd){
	// 비밀번호 설정 //
	$user_pw = Encrypt($wp_passwd);
	array_push($fields,"wp_passwd");
	array_push($values,"$user_pw");
}

// DB 저장 //
$insert = $Mysql->Update(wp_club_ctrl,$fields,$values," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>