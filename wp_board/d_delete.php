<?
// Session 시작 //
require_once "../wp_object/head_pop.html";

// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($code);

// 비밀번호 체크 //
Pw_Check($passwd);

// 답변글 존재하면 삭제불가 //
if(!$allow_delete_thread)
{
	$query = "SELECT thread FROM wp_board WHERE fid='$fid' AND length(thread) = length('$thread')+1 AND locate('$thread',thread) = 1 ORDER BY thread DESC LIMIT 1";
	$Mysql->ResultQuery($query);
	if($Mysql->row)
	{
		Error("THREAD_ERROR");
		exit;
	}
}

// 관리자 확인 //
if($member_auth < "8") { //매너저, 관리자가 아니면.. 수정시 패스워드 인증거침

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
	if(strcmp($real_pass,$user_pass)) //수정불가
	{
		Error("PW_ERROR");
		exit;
	}
}

// 파일 삭제 //
$file_q = "SELECT uid, real_file, change_file, down FROM wp_file WHERE t_name = '$code' AND t_uid = '$uid' ";
$f_res = mysql_query($file_q);
// 첨부 파일 //
while($obj = mysql_fetch_object($f_res))
{
	// 파일 삭제 //
	$del_file = "$file_path$obj->change_file";
	unlink("$del_file");
	// DB 삭제//
	$update = $Mysql->Delete(wp_file,"WHERE uid='$obj->uid'");

}
// DB 삭제 //
$query = "DELETE FROM wp_board WHERE fid=$fid AND thread='$thread'";
$result = $Mysql->Query($query);
if(!$result)
{
	Error("QUERY_ERROR");
	exit;
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>