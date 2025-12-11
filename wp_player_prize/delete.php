<?
// Session 시작 //
require_once "../wp_library/head.php";


// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
//Method_Check($REQUEST_METHOD);
/*
// 관리자 확인 //
if(!session_is_registered("Site_Admin") || !session_is_registered("Admin_Id"))
{
	if($Admin_Id != md5($Site_Admin."kodaewoong"))
	{
		Error("ADMIN_ERROR");
		exit;
	}
	else
	{
		Error("ACTION_ERROR");
		exit;
	}
}
*/
// DB 저장 //
$insert = $Mysql->Delete(wp_player_prize," where uid='$uid'");

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get&mode=$mode&isuse=$isuse&player=$player'>");
?>