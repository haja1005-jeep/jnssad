<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($city);
Null_Check($code);
Null_Check($wp_id);
Null_Check($wp_passwd);

// 빈칸 체크 //
Input_Check($city);
Input_Check($code);
Input_Check($wp_id);
Input_Check($wp_passwd);

// 비밀번호 설정 //
$user_pw = Encrypt($wp_passwd);

// TB 필드명 //
$fields = array("code","name","city","wp_id","wp_passwd","auth","level","isuse","comment","signdate");

// TB 필드값 //
$values = array("$code","$name","$city","$wp_id","$user_pw","$auth","$level","0","$comment","$signdate");

// DB 저장 //
$insert = $Mysql->Insert(wp_club_ctrl,$fields,$values);

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>