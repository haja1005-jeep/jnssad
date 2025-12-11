<?
// Session 시작 //
require_once "../wp_library/head.php";

if($Admin_auth != "top"){
	ERROR("AUTH_ERROR");
	exit;
}
// DB 저장 //
$insert = $Mysql->Delete(wp_club_apply," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=apply_list'>");
?>