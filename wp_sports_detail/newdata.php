<?
// Session 시작 //
require_once "../wp_library/head.php";

// 전송 체크 //
Method_Check($REQUEST_METHOD);

// 공백 체크 //
Null_Check($org_game);
Null_Check($tar_game);

// 빈칸 체크 //
Input_Check($org_game);
Input_Check($tar_game);

// 기존 등록된 데이터 삭제 //
$Mysql->Delete(wp_sports_event_detail, " WHERE gam_uid = '$tar_game'");

$que = "select * from wp_sports_event_detail where gam_uid = '$org_game' AND isuse = '0' ORDER BY isuse asc, spo_code asc, code asc, sex asc,code_detail asc,orderby asc";
$res = mysql_query($que);
while($obj = mysql_fetch_object($res))
{
	// TB 필드명 //
	$fields = array("gam_uid","game_kind","spo_code","code","name","sex","code_detail","code_detail_name","bigo","isuse","orderby");

	// TB 필드값 //
	$values = array("$tar_game","$obj->game_kind","$obj->spo_code","$obj->code","$obj->name","$obj->sex","$obj->code_detail","$obj->code_detail_name","$obj->bigo","$obj->isuse","$obj->orderby");

	// DB 저장 //
	$insert = $Mysql->Insert(wp_sports_event_detail,$fields,$values);
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>