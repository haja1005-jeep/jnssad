<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 중복 체크 //
$r_que = "SELECT * FROM wp_team_agree WHERE wp_id = '$agree_id'";
$r_res = mysql_query($r_que);
$r_num = mysql_num_rows($r_res);
$r_obj = mysql_fetch_object($r_res);

if($r_num > 0)
{
	// TB 필드명 //
	$fields = array("agree","comment");

	// TB 필드값 //
	$values = array("$agree","$comment");

	// DB 수정 //
	$insert = $Mysql->Update(wp_team_agree,$fields,$values," where uid='$r_obj->uid'");
}
else
{
	// TB 필드명 //
	$fields = array("wp_id","agree","comment");

	// TB 필드값 //
	$values = array("$agree_id","$agree","$comment");

	// DB 저장 //
	$insert = $Mysql->Insert(wp_team_agree,$fields,$values);
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=input'>");
?>