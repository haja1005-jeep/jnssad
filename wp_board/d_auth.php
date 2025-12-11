<?
// Session 시작 //
require_once "../wp_object/head.html";

// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// DB 생성 //
$Mysql = new Mysql;

// DB 연결 //
$Mysql->Connect();

// 공백 체크 //
Null_Check($code);

// 비밀번호 체크 //
Pw_Check($passwd);

// 레코드 쿼리 //
$result = mysql_query("SELECT passwd FROM wp_board WHERE uid='$uid'");
if(!$result)
{
	Error("QUERY_ERROR");
	exit;
}
$real_pass = mysql_result($result,0,0);

// 비밀번호 암호화 //
$user_pass = Encrypt($passwd);
// 비밀번호 확인 //
if(!strcmp($real_pass,$user_pass))
{
	// 인증 암호화 //
	$auth = Encrypt(auth);
	//보기, 수정
	// 페이지 이동 //
	echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=view&uid=$uid'>");
}
else
{
	Error("PW_ERROR");
	exit;
}
?>