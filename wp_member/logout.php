<?
// Session 시작 //
require_once "../wp_library/head.php";

// 경로 체크 //
Referer_Check($admin_domain);

// 관리자 인증 //
if(!session_is_registered("Site_Admin") || !session_is_registered("Admin_Id"))
{
	Error("ADMIN_ERROR");
	exit;
}
else
{
	// 변수 삭제 //
	$old_id = $Site_Admin;
	$result1 = session_unregister("Site_Admin");
	$result2 = session_unregister("Admin_Id");
	$result3 = session_unregister("Admin_auth");
	$result4 = session_unregister("Admin_code");

	// 세션 삭제 //
	session_destroy();

	// 페이지 이동 //
	if(!empty($old_id))
	{
		if($result1 && $result2 && $result3 && $result4)
		{
			echo ("<meta http-equiv='Refresh' content='0; URL=../index.html'>");
		}
		else
		{
			echo ("<p align='center'><font color='#3366cc'><b>관리자에게 문의 바랍니다.!!!!</b></font></p>");
		}
	}
	else
	{
		echo ("<p align='center'><font color='#ff6600'><b>관리자에게 문의 바랍니다.!!!!</b></font></p>");
	}
}
?>