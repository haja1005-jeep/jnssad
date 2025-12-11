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

//사이트권한 관리 클래스
class_exists('Site_Auth') || require('../wp_library/Site_Auth_class.php');
$site_auth = new Site_Auth;

// 경로 체크 //
Referer_Check($admin_domain);

// 장애인 체육회만 설정 게시판 권한 설정하기.
if($Admin_auth == "all") $member_auth = "0";
if($Admin_auth == "normal") $member_auth = "1";
if($Admin_auth == "sponsor") $member_auth = "1";
if($Admin_auth == "top") $member_auth = "9";
//================================

?>
<html>
<head>
<title><?=$domain_name?> 관리자모드</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script language="javascript" src="../wp_config/calendar.js"></script>
<script language="javascript" src="../wp_config/script.js"></script>
<script language="javascript" src="../wp_config/check.js"></script>
<script language="javascript" src="../wp_config/NumberFormat.js"></script>

<link type="text/css" rel="stylesheet" href="../wp_config/style.css">
<link type="text/css" rel="stylesheet" href="../wp_config/calendar.css">
</head>