<?
// Session 시작 //
require_once "../wp_library/head.php";

// 경로 체크 //
Referer_Check($admin_domain);

// DB 저장 //
$delete = $Mysql->Delete(wp_sports_event," where uid='$uid'");
if($delete){
	$delete = $Mysql->Delete(wp_sports_event_detail," where gam_uid='$gam_uid' AND spo_code='$spo_code'");
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>