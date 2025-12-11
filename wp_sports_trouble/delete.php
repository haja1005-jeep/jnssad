<?
// Session 시작 //
require_once "../wp_library/head.php";

// DB 저장 //
$insert = $Mysql->Delete(wp_trouble_ctrl," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>