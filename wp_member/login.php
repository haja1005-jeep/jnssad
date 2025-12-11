<?
// Session 시작 //
require_once "../wp_library/head_0.php";

// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 아이디 체크 //
Id_Check($wp_id);

// 비밀번호 체크 //
Pw_Check($wp_passwd);

// DB 생성 //
$Mysql = new Mysql;

// DB 연결 //
$Mysql->Connect();

/*
if($wp_id != "jnsad" && $wp_id != "mokpo"){
	Error("ID_ERROR");
	exit;
}
*/
if($wp_kind == "club"){
	/*
	echo ("<script>
			alert('현재 선수등록시스템이 점검 중 입니다.');
		   </script>
		 ");
	echo ("<meta http-equiv='Refresh' content='0; URL=../wp_main/intro.html'>");
	*/

	// 레코드 쿼리 //
	$query = "SELECT code, wp_id, wp_passwd, auth wp_auth,name,level FROM wp_club_ctrl WHERE wp_id='$wp_id' AND auth in('sponsor','normal') AND isuse = '0'";
} else if($wp_kind == "player"){
	/*
	echo ("<script>
			alert('현재 선수등록시스템이 점검 중 입니다.');
		   </script>
		 ");
	echo ("<meta http-equiv='Refresh' content='0; URL=../wp_main/intro.html'>");
	*/

	// 레코드 쿼리 //
	$query = "SELECT wp_id, wp_passwd, 'user' wp_auth,name FROM wp_player_ctrl WHERE wp_id='$wp_id'";
} else if($wp_kind == "admin"){

	// 레코드 쿼리 //
	$query = "SELECT code, wp_id, wp_passwd, 'top' wp_auth,name,level FROM wp_club_ctrl WHERE wp_id='$wp_id' AND auth = 'top' AND isuse = '0'";
} else if($wp_kind == "s_group"){
	/*
	echo ("<script>
			alert('현재 선수등록시스템이 점검 중 입니다.');
		   </script>
		 ");
	echo ("<meta http-equiv='Refresh' content='0; URL=../wp_main/intro.html'>");
	*/
	// 레코드 쿼리 //
	$query = "SELECT code, wp_id, wp_passwd, 's_group' wp_auth,name,level FROM wp_club_ctrl WHERE wp_id='$wp_id' AND auth = 's_group' AND isuse = '0'";
}

$Mysql->ResultQuery($query);

if(!$Mysql->row)
{
	Error("ID_ERROR");
	exit;
}
else
{
	// 입력비밀번호 암호화 //
	$user_pw = Encrypt($wp_passwd);



	// 레코드 결과 //
	$row = mysql_fetch_object($Mysql->result);
	$Admin_PW = $row->wp_passwd;




	// 비밀번호 비교 //
	if(strcmp($Admin_PW,$user_pw))
	{
		Error("PW_ERROR");
		exit;
	}
	else
	{
		// 관리자 세션 실행 //
		session_unregister("Site_Admin");
		session_unregister("Admin_Id");
		session_unregister("Admin_auth");
		session_unregister("Admin_code");
		session_unregister("Admin_name");
		session_unregister("Admin_level");
		$Site_Admin = $wp_id;
		$Admin_name = $row->name;
		$Admin_Id  = md5($wp_id.$site_md5);
		$Admin_auth = $row->wp_auth;
		$Admin_code = $row->code; //시/군, 클럽 코드
		$Admin_level = $row->level; //권한레벨
		session_register ("Site_Admin");
		session_register ("Admin_Id");
		session_register ("Admin_name");
		session_register ("Admin_auth");
		session_register ("Admin_code");
		session_register ("Admin_level");

		// 페이지 이동 //
		echo ("<meta http-equiv='Refresh' content='0; URL=../wp_main/index.html'>");
	}
}
?>