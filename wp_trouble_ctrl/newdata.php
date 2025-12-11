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
$Mysql->Delete(wp_trouble_ctrl, " WHERE gam_uid = '$tar_game'");

$que = "select * from wp_trouble_ctrl where gam_uid = '$org_game' AND isuse = '0' ORDER BY spo_code asc, code asc, orderby asc, level_code asc";
$res = mysql_query($que);
while($obj = mysql_fetch_object($res))
{
	// TB 필드명 //
	$fields = array("gam_uid","spo_code","code","name","level_code","level_name","bigo","isuse","orderby");

	// TB 필드값 //
	$values = array("$tar_game","$obj->spo_code","$obj->code","$obj->name","$obj->level_code","$obj->level_name","$obj->bigo","$obj->isuse","$obj->orderby");

	// DB 저장 //
	$insert = $Mysql->Insert(wp_trouble_ctrl,$fields,$values);
}

// 페이지 이동 //
echo ("<meta http-equiv='Refresh' content='0; URL=index.html?$basic_get$add_get&mode=list'>");
?>