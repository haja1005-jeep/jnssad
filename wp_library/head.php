<?
// Session 시작 //
session_start();

//권한 관리 클래스
class_exists('Auth') || require('../wp_library/Board_Auth_class.php');
$auth_class = new Auth;

// Mysql 클래스 //
require_once "../wp_library/mysql_class.php";
// DB 생성 //
$Mysql = new Mysql;
// DB 연결 //
$Mysql->Connect();

// Upload 클래스 //
require_once "../wp_library/upload_class.php";

// Paging 클래스 //
require_once "../wp_library/page_class.php";

// Error 함수 //
require_once "../wp_library/check.php";

// Setup 설정 //
require_once "../wp_library/setup.php";

// 토너먼트 대진표정보 클래스  //
require_once "../wp_library/tournament_class.php";

//사이트권한 관리 클래스
class_exists('Site_Auth') || require('../wp_library/Site_Auth_class.php');
$site_auth = new Site_Auth;

// 경로 체크 //
Referer_Check($admin_domain);

if(!session_is_registered("Site_Admin") || !session_is_registered("Admin_Id") || !session_is_registered("Admin_Lv"))
{
	if($Admin_Id != md5($Site_Admin."kodaewoong"))
	{
		Error("ADMIN_ERROR");
		exit;
	}
	else
	{
		//Error("ACTION_ERROR");
//		exit;
	}
}
//========= 사이트 별로 권한 설정 변경
//=== 게시판에서 사용

if($Admin_auth == "normal") $member_auth = "7";
if($Admin_auth == "sponsor") $member_auth = "8";
if($Admin_auth == "top") $member_auth = "9";

//관리자 이름 셋팅 - 사이트 별로 셋팅 게시판 글쓰기등 자동으로 성명 및 ID메일 주소 셋팅..
if($member_auth >= "9"){
	$member_id = $Site_Admin ;
	$member_name = "전장체";
	$member_email = "webmaster@jnsad.or.kr";
	$member_pwd = "jnsad";
}
//================================

?>
<html>
<head>
<title><?=$domain_name?> 관리자모드</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="Robots" content="noindex,nofollow">
<script language="javascript" src="../wp_config/calendar.js"></script>
<script language="javascript" src="../wp_config/script.js"></script>
<script language="javascript" src="../wp_config/check.js"></script>
<script language="javascript" src="../wp_config/NumberFormat.js"></script>

<link type="text/css" rel="stylesheet" href="../wp_config/style.css">
<link type="text/css" rel="stylesheet" href="../wp_config/calendar.css">
</head>