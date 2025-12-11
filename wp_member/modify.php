<?
// Session 시작 //
require_once "../wp_library/head.php";


// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
Method_Check($REQUEST_METHOD);
// 공백 체크 //
Null_Check($wp_id);
Null_Check($wp_passwd);
Null_Check($new_passwd);
Null_Check($re_new_passwd);

// 빈칸 체크 //
Input_Check($wp_id);
Input_Check($wp_passwd);
Input_Check($new_passwd);
Input_Check($re_new_passwd);

//로그인 체크
if(!$site_auth->isLogin()){
	Error("ADMIN_ERROR");
	exit;
}
//권한 체크 - 관리자, 주최관리자만 볼 수 있음
if(!$site_auth->isConn($cd_player,$Admin_auth)){
	Error("AUTH_ERROR");
	exit;
}
if($Admin_auth == "user"){
	$query = "SELECT wp_id, wp_passwd,name FROM wp_player_ctrl WHERE wp_id = '$wp_id'";
}else{
	$query = "SELECT wp_id, wp_passwd,name FROM wp_club_ctrl WHERE wp_id = '$wp_id'";
}

$result = mysql_query($query);
if (!$result) {
   	Error("DB_ERROR");
   	exit;
}
if(mysql_num_rows($result) <= 0){
   	Error("NOT_DATA");
	exit;
}
$row = mysql_fetch_object($result);

// 비밀번호 설정 //
$chk_pw = Encrypt($wp_passwd);

if(strcmp($chk_pw, $row->wp_passwd) != 0){
	Error("PW_ERROR");
	exit;
}

if(strcmp($new_passwd, $re_new_passwd) != 0){
	Error("NEW_PW_ERROR");
	exit;
}

// 비밀번호 설정 //
$user_pw = Encrypt($new_passwd);

// TB 필드명 //
$fields = array("wp_passwd");
// TB 필드값 //
$values = array($user_pw);

if($Admin_auth == "user"){
	// DB 저장 //
	$insert = $Mysql->Update(wp_player_ctrl,$fields,$values," where wp_id='$wp_id'");
}else{
	// DB 저장 //
	$insert = $Mysql->Update(wp_club_ctrl,$fields,$values," where wp_id='$wp_id'");
}

// 페이지 이동 //
echo ("<script>
		alert('회원님의 정보가 정상적으로 변경되었습니다');
	   </script>
	 ");
echo ("<meta http-equiv='Refresh' content='0; URL=modify.html'>");
?>