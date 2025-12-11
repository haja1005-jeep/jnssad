<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($level_code);

// 빈칸 체크 //
Input_Check($level_code);

$name = $wp_trouble_code[$code];
// TB 필드명 //
$fields = array("spo_code","code","name","level_code","level_name","bigo","orderby","isuse");

// TB 필드값 //
$values = array("$spo_code","$code","$name","$level_code","$level_name","$bigo","$orderby","$isuse");

// DB 저장 //
$insert = $Mysql->Insert(wp_sports_trouble,$fields,$values);
//$insert = $Mysql->Update(wp_sports_trouble,$fields,$values," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>