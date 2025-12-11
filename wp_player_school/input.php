<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($kind);
Null_Check($s_name);

// 빈칸 체크 //
Input_Check($kind);

// TB 필드명 //
$fields = array("id","kind","s_name","c_name","grade","f_date","t_date");

// TB 필드값 //
$values = array("$player","$kind","$s_name","$c_name","$grade","$f_date","$t_date");

// DB 저장 //
$insert = $Mysql->Insert(wp_player_school,$fields,$values);

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list&player=$player'>");
?>