<?
// Session 시작 //
require_once "../wp_library/head_admin.php";

// 공백 체크 //
Null_Check($code);

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
echo ("<meta http-equiv='Refresh' content='0; URL=admin.html?$basic_get$add_get&mode=list'>");
?>