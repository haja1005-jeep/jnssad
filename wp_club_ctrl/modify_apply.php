<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// TB 필드명 //
$fields = array("name", "comment", "status", "signdate2");

// TB 필드값 //
$values = array("$name", "$comment", "$status", "$signdate");

// DB 저장 //
$update = $Mysql->Update(wp_club_apply,$fields,$values," where uid='$uid'");

// 수정이 완료되면 시, 군 및 클럽, 단체관리 테이블에 추가 //
if($status == "1" && $update)
{
	// 레코드 쿼리 //
	$query = "SELECT * FROM wp_club_apply WHERE uid = '$uid'";
	$result = mysql_query($query);
	$obj = mysql_fetch_object($result);

	// 중복처리 쿼리 //
	$c_que = "SELECT * FROM wp_club_ctrl WHERE wp_id = '$obj->wp_id'";
	$c_res = mysql_query($c_que);
	$c_cnt = mysql_num_rows($c_res);

	if($c_cnt < 1)
	{
		// TB 필드명 //
		$fields = array("code","name","city","wp_id","wp_passwd","auth","level","isuse","comment","signdate2");

		// TB 필드값 //
		$values = array("$obj->code","$obj->name","$obj->city","$obj->wp_id","$obj->wp_passwd","$obj->auth","$obj->level","0","$obj->comment","$signdate");

		// DB 저장 //
		$insert = $Mysql->Insert(wp_club_ctrl,$fields,$values);
	}
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=apply_list&jnsad=$jnsad'>");
?>