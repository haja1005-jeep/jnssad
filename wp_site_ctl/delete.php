<?
// Session 시작 //
require_once "../library/head.php";

// 경로 체크 //
Referer_Check($admin_domain);

//삭제 //
$update = $Mysql->Delete(wp_board_ctl,"WHERE uid='$uid'");
if(!$update)
{
	Error("QUERY_ERROR");
	exit;
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?code=$code&mode=list&type=$type&page=$page&keyfield=$keyfield&key=$key'>");
?>