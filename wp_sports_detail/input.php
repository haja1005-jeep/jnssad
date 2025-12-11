<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($gam_uid);
Null_Check($spo_code);
Null_Check($code);
Null_Check($code_detail);

// 빈칸 체크 //
Input_Check($spo_code);
Input_Check($code);
Input_Check($code_detail);

//영어체크
Eng_Check($spo_code);
Eng_Check($code);

// TB 필드명 //
$fields = array("gam_uid","game_kind","spo_code","code","name","sex","code_detail","code_detail_name","bigo","isuse","orderby","player_cnt");

// TB 필드값 //
$values = array("$gam_uid","$game_kind","$spo_code","$code","$name","$sex","$code_detail","$code_detail_name","$bigo","0","$orderby","$player_cnt");

// DB 저장 //
$insert = $Mysql->Insert(wp_sports_event_detail,$fields,$values);

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?spo_code=$spo_code&code=$code'>");
?>