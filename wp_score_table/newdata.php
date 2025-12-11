<?
// Session 시작 //
require_once "../wp_library/head.php";

// 경로 체크 //
Referer_Check($admin_domain);

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 기존 종목 정보 삭제 //
$delete = $Mysql->Delete(wp_score_table," where gam_uid='$tar_game'");

// 경기 종목 가져오기 //
$query = "SELECT * FROM wp_score_table WHERE gam_uid = '$org_game' ORDER BY uid ASC";
$result = mysql_query($query);

while($obj = mysql_fetch_object($result))
{

	// TB 필드명 //
	$fields = array("gam_uid","spo_code","game_kind","player_cnt","score_1","score_2","score_3","score_4","score_5","score_6","score_7","score_8","sort","note");

	// TB 필드값 //
	$values = array("$tar_game","$obj->spo_code","$obj->game_kind","$obj->player_cnt","$obj->score_1","$obj->score_2","$obj->score_3","$obj->score_4","$obj->score_5","$obj->score_6","$obj->score_7","$obj->score_8","$obj->sort","$obj->note");

	// DB 저장 //
	$insert = $Mysql->Insert(wp_score_table,$fields,$values);
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?mode=input'>");
?>