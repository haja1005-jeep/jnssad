<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($spo_code);
Null_Check($code);
Null_Check($code_detail);

// 빈칸 체크 //
Input_Check($spo_code);
Input_Check($code);
Input_Check($code_detail);

// TB 필드명 //
$fields = array("spo_code","game_kind","code","name","sex","code_detail","code_detail_name","bigo","orderby","isuse","player_cnt");

// TB 필드값 //
$values = array("$spo_code","$game_kind","$code","$name","$sex","$code_detail","$code_detail_name","$bigo","$orderby","$isuse","$player_cnt");

// DB 저장 //
$update = $Mysql->Update(wp_sports_event_detail,$fields,$values," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>