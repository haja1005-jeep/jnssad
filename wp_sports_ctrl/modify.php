<?
// Session 시작 //
require_once "../wp_library/head.php";

// 경로 체크 //
Referer_Check($admin_domain);
// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($code);

// 빈칸 체크 //
Input_Check($code);

// TB 필드명 //
$fields = array("code","name","method","detail","orderby","isuse","gam_kind","gam_type");

// TB 필드값 //
$values = array("$code","$name","$method","$detail","$orderby","$isuse","$gam_kind","$gam_type");

// DB 저장 //
$update = $Mysql->Update(wp_sports_event,$fields,$values," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>